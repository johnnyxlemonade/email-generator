<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Lemonade\EmailGenerator\Component\{Badge, BulletList, Button, Columns, Divider, Group, Heading, Image, Spacer, Table, Text};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;
use Lemonade\EmailGenerator\Presentation\Renderer\{BadgeRenderer, BulletListRenderer, ButtonRenderer, ColumnsRenderer, DividerRenderer, GroupRenderer, HeadingRenderer, ImageRenderer, SpacerRenderer, TableRenderer, TextRenderer};
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Color;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Exercises individual renderers and the shared dispatch and row layout boundary
 */
final class ComponentRendererTest extends EmailTestCase
{
    /**
     * @template T of ComponentInterface
     *
     * @param HtmlComponentRendererInterface<T> $renderer
     * @param T $component
     */
    #[DataProvider('renderers')]
    public function testIndividualRenderer(HtmlComponentRendererInterface $renderer, ComponentInterface $component, string $expected): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $result = $renderer->render($component, $context, new ComponentRenderer($context), 300);
        self::assertNotNull($result);
        self::assertStringContainsString($expected, $result->html());
        self::assertStringNotContainsString('<script>', $result->html());
        self::assertStringStartsNotWith('<tr><td', $result->html());
    }

    public function testDefaultDispatcherMapsEveryComponentToItsRenderer(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $dispatcher = new ComponentRenderer($context, 300);
        foreach (self::renderers() as [, $component, $expected]) {
            self::assertStringContainsString($expected, $dispatcher->render($component));
        }
    }

    /**
     * @return iterable<string, array{HtmlComponentRendererInterface<*>, ComponentInterface, string}>
     */
    public static function renderers(): iterable
    {
        yield 'heading' => [new HeadingRenderer(), new Heading('<script>', 2), '>&lt;script&gt;</h2>'];
        yield 'text' => [new TextRenderer(), new Text("<script>\nNext"), '<p style="margin:0;">&lt;script&gt;<br>Next</p>'];
        yield 'image' => [new ImageRenderer(), new Image('https://example.com/image.png', '<script>', 1000, 500), 'alt="&lt;script&gt;" width="300" height="150"'];
        yield 'button' => [new ButtonRenderer(), new Button('<script>', 'https://example.com/?a=1&b=2'), 'href="https://example.com/?a=1&amp;b=2"'];
        yield 'list' => [new BulletListRenderer(), new BulletList(['<script>'], true), '<ol style="margin:0;padding:0 24px;">'];
        yield 'table' => [new TableRenderer(), new Table(['<script>'], [['Cell']]), '<th scope="col"'];
        yield 'divider' => [new DividerRenderer(), new Divider(new Color('abc'), 2), 'height="2" bgcolor="#aabbcc"'];
        yield 'spacer' => [new SpacerRenderer(), new Spacer(23), '&nbsp;'];
        yield 'badge' => [new BadgeRenderer(), new Badge('<script>'), '>&lt;script&gt;</td>'];
        yield 'columns' => [new ColumnsRenderer(), new Columns([new Text('<script>')], [new Text('Second')]), 'class="email-columns"'];
        yield 'group' => [new GroupRenderer(), new Group([new Text('<script>')], new Color('abc')), 'class="email-group"'];
    }

    public function testUnknownComponentIsRejectedByDispatcher(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        try {
            (new ComponentRenderer($context))->render(new class implements ComponentInterface {});
            self::fail('Unknown component was accepted.');
        } catch (CompositionException $exception) {
            self::assertSame(ErrorCode::ComponentUnsupported, $exception->errorCode());
        }
    }

    public function testRegisteredRendererIsUsedThroughMultipleGroupLevels(): void
    {
        $component = new class implements ComponentInterface {};
        $renderer = new class implements HtmlComponentRendererInterface {
            public function render(ComponentInterface $component, RenderContext $context, ComponentRenderer $dispatcher, int $contentWidth): RenderedComponent
            {
                return new RenderedComponent('Available width ' . $contentWidth);
            }
        };
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $dispatcher = new ComponentRenderer($context, 300, [
            $component::class => $renderer,
            Group::class => new GroupRenderer(),
        ]);
        $group = new Group([
            new Group([$component], padding: 20, borderColor: new Color('abc')),
            $component,
        ], padding: 10);
        $html = $dispatcher->renderComponents(ComponentExpander::expand([$group, $component]));

        self::assertStringContainsString('Available width 238', $html);
        self::assertStringContainsString('Available width 280', $html);
        self::assertStringContainsString('Available width 300', $html);
        self::assertSame(2, substr_count($html, 'class="email-group"'));
        self::assertSame(2, substr_count($html, 'padding:0 0 0px;'));
        self::assertSame(3, substr_count($html, 'padding:0 0 16px;'));
    }

    public function testLastVisibleComponentAndEmptyContentSpacing(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $dispatcher = new ComponentRenderer($context);
        self::assertSame('', $dispatcher->render(new Group()));
        self::assertStringContainsString('padding:0 0 16px;', $dispatcher->render(new BulletList([])));
        self::assertStringContainsString('padding:0 0 0px;', $dispatcher->render(new Text('Last'), true));
        self::assertSame(
            $dispatcher->render(new Text('Last'), true),
            $dispatcher->renderComponents([new Group(), new Text('Last'), new Group()], trimTrailingSpacing: true),
        );
        self::assertStringContainsString('padding:0 0 16px;', $dispatcher->renderComponents([new Text('Last')]));
    }

    public function testSpacerHasNoAdditionalPadding(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $dispatcher = new ComponentRenderer($context);
        $spacer = new Spacer(23);
        $expected = '<tr><td height="23" aria-hidden="true" style="height:23px;font-size:1px;line-height:23px;mso-line-height-rule:exactly;">&nbsp;</td></tr>' . "\n";
        self::assertSame($expected, $dispatcher->render($spacer));
        self::assertSame($expected, $dispatcher->render($spacer, true));
        self::assertSame(23, (new SpacerRenderer())->render($spacer, $context, $dispatcher, 300)->height());
    }
}
