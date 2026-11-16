<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Lemonade\EmailGenerator\Component\{BulletList, Columns, Group, Image, Spacer, Text};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;
use Lemonade\EmailGenerator\Presentation\Renderer\ColumnsRenderer;
use Lemonade\EmailGenerator\Presentation\Renderer\GroupRenderer;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Color;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

/**
 * Protects column sizing, responsive markup, immutable snapshots and expansion limits
 */
final class ColumnsTest extends EmailTestCase
{
    public function testColumnsDetachInputReferences(): void
    {
        $first = new Text('First');
        $second = new Text('Second');
        $firstInput = [&$first];
        $secondInput = [&$second];
        $columns = new Columns($firstInput, $secondInput, gap: 23);
        $originalFirst = $first;
        $originalSecond = $second;
        $first = new Text('Changed first');
        $second = new Text('Changed second');
        $firstInput[] = new Text('Added');
        $secondInput[0] = new Text('Replaced');
        $copy = $columns->first();
        $copy[0] = new Text('Getter change');
        $copy = $columns->second();
        $copy[0] = new Text('Getter change');
        self::assertSame([$originalFirst], $columns->first());
        self::assertSame([$originalSecond], $columns->second());
        self::assertSame(23, $columns->gap());
        self::assertTrue((new ReflectionClass($columns))->isReadOnly());
        self::assertTrue((new ReflectionClass($columns))->isFinal());
    }

    public function testNestedWidthsAndOutlookCells(): void
    {
        $image = new Image('https://example.com/image.png?a=1&b=2', '<Alt>', 1000, 500);
        $html = $this->emails->create()->add(new Group([
            new Columns(
                first: [new Group([$image], padding: 7, borderColor: new Color('abc'))],
                second: [new Columns([$image], [$image], gap: 9)],
                gap: 17,
            ),
            $image,
        ], padding: 10, borderColor: new Color('ccc')))->add($image)->render();
        // 536 - 20 - 2 = 514; 514 - 17 = 497, split into 248 and 249.
        // First group: 248 - 14 - 2 = 232; nested columns: (249 - 9) / 2 = 120.
        $xpath = $this->xpath($html);
        $images = $xpath->query('//img');
        self::assertNotFalse($images);
        self::assertSame(5, $images->length);
        foreach ([232, 120, 120, 514, 536] as $index => $width) {
            self::assertSame((string) $width, $xpath->evaluate('string((//img)[' . ($index + 1) . ']/@width)'));
            self::assertSame((string) ($width / 2), $xpath->evaluate('string((//img)[' . ($index + 1) . ']/@height)'));
        }
        self::assertStringContainsString('<!--[if mso]><table role="presentation" width="514"', $html);
        self::assertStringContainsString('<td width="248" valign="top"><![endif]-->', $html);
        self::assertStringContainsString('<td width="17" aria-hidden="true"', $html);
        self::assertStringContainsString('<td width="249" valign="top"><![endif]-->', $html);
        self::assertStringContainsString('max-width:248px;', $html);
        self::assertStringContainsString('max-width:249px;', $html);
        self::assertStringContainsString('src="https://example.com/image.png?a=1&amp;b=2" alt="&lt;Alt&gt;"', $html);
    }

    public function testResponsiveMarkupAndSpacing(): void
    {
        $html = $this->emails->create()->add(new Columns(
            first: [new Text('First'), new Text('Last'), new Group()],
            second: [new Text('Before spacer'), new Spacer(11), new Columns([], [])],
            gap: 23,
        ))->render();
        self::assertStringContainsString('@media only screen and (max-width:600px)', $html);
        self::assertStringContainsString('.email-column{display:block!important;max-width:100%!important;}', $html);
        self::assertStringContainsString('.email-column-gap-23{display:block!important;width:100%!important;height:23px!important;}', $html);
        self::assertStringContainsString('display:inline-block;vertical-align:top;width:100%;max-width:256px;', $html);
        self::assertStringContainsString('display:inline-block;vertical-align:top;width:100%;max-width:257px;', $html);
        $xpath = $this->xpath($html);
        self::assertStringContainsString('padding:0 0 16px;', (string) $xpath->evaluate('string(//p[text()="First"]/../@style)'));
        self::assertStringContainsString('padding:0 0 0px;', (string) $xpath->evaluate('string(//p[text()="Last"]/../@style)'));
        self::assertStringContainsString('height:11px;font-size:1px;line-height:11px;mso-line-height-rule:exactly;', $html);
        self::assertSame(1.0, $xpath->evaluate('count(//td[@height="11"])'));
    }

    public function testEmptyColumnsAndLists(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $dispatcher = new ComponentRenderer($context);
        self::assertSame('', $dispatcher->render(new Columns([], [])));
        self::assertSame('', $dispatcher->render(new Columns([new Group()], [new Columns([], [])])));
        $html = $dispatcher->render(new Columns([], [new BulletList([])]));
        self::assertSame(2, substr_count($html, 'class="email-column"'));
        self::assertStringContainsString('max-width:260px;', $html);
        self::assertStringContainsString('padding:0 0 0px;', $html);
        self::assertStringNotContainsString('<ul', $html);
    }

    public function testNestedRenderingRetainsDispatcherRegistrations(): void
    {
        $component = new class implements ComponentInterface {};
        $renderer = new class implements HtmlComponentRendererInterface {
            /**
             * Exposes the width forwarded through nested containers
             */
            public function render(ComponentInterface $component, RenderContext $context, int $contentWidth, ComponentRenderer $dispatcher): RenderedComponent
            {
                return new RenderedComponent('Available width ' . $contentWidth);
            }
        };
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $dispatcher = new ComponentRenderer($context, 301, [
            $component::class => $renderer,
            Columns::class => new ColumnsRenderer(),
            Group::class => new GroupRenderer(),
        ]);
        $html = $dispatcher->renderComponents(ComponentExpander::expand([
            new Columns([new Group([$component], padding: 5)], [$component], gap: 16),
            $component,
        ]));
        self::assertStringContainsString('Available width 132', $html);
        self::assertStringContainsString('Available width 143', $html);
        self::assertStringContainsString('Available width 301', $html);
    }

    public function testCompositionsAreSnapshottedAcrossDocumentBranches(): void
    {
        $composition = new class implements CompositionInterface {
            public string $text = 'Original';

            /**
             * Produces content from the current mutable test state
             */
            public function components(): iterable
            {
                yield new Text($this->text);
            }
        };
        $columns = new Columns([$composition], [new Group([$composition])]);
        $base = $this->emails->create()->add($columns);
        $before = $base->render();
        $composition->text = 'Changed';
        $branch = $base->text('Branch');
        self::assertSame($before, $base->render());
        self::assertSame(2, substr_count($branch->render(), '>Original</p>'));
        self::assertStringNotContainsString('Changed', $branch->render());
        self::assertStringNotContainsString('Branch', $base->render());
        self::assertStringContainsString('Changed', $this->emails->create()->add($columns)->render());
    }

    public function testCyclesThroughColumnsAreRejectedAtomically(): void
    {
        foreach ([true, false] as $first) {
            $cycle = new class ($first) implements CompositionInterface {
                /**
                 * Selects the column that introduces a cycle
                 */
                public function __construct(private bool $first) {}

                /**
                 * Returns a container referencing this composition
                 */
                public function components(): iterable
                {
                    yield new Columns($this->first ? [$this] : [], $this->first ? [] : [$this]);
                }
            };
            $base = $this->emails->create()->text('Keep');
            try {
                $base->add(new Columns([new Text('Discard')], [$cycle]));
                self::fail('A cycle through columns was accepted.');
            } catch (CompositionException $exception) {
                self::assertSame(ErrorCode::CompositionCycle, $exception->errorCode());
            }
            self::assertCount(1, $base->components());
        }
    }

    public function testExpansionLimits(): void
    {
        $nested = new Columns([new Text('Deep')], []);
        for ($depth = 0; $depth < 33; ++$depth) {
            $nested = new Columns([], [$nested]);
        }
        $wide = new Columns(array_fill(0, 5000, new Text('First')), array_fill(0, 5000, new Text('Second')));
        foreach ([[$nested, ErrorCode::CompositionDepthExceeded], [$wide, ErrorCode::CompositionSizeExceeded]] as [$columns, $code]) {
            try {
                $this->emails->create()->add($columns);
                self::fail('An expansion limit was bypassed.');
            } catch (CompositionException $exception) {
                self::assertSame($code, $exception->errorCode());
            }
        }
    }

    public function testExhaustedWidthAndSmallestValidWidth(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $columns = new Columns([new Text('First')], [new Text('Second')]);
        self::assertSame(2, substr_count((new ComponentRenderer($context, 18))->render($columns), 'max-width:1px;'));
        self::assertStringContainsString('max-width:1px;', (new ComponentRenderer($context, 2))->render(new Columns([new Text('Zero gap')], [], gap: 0)));
        foreach ([17, 16, 0, -1] as $width) {
            try {
                (new ComponentRenderer($context, $width))->render($columns);
                self::fail('Exhausted width was accepted.');
            } catch (InvalidInputException $exception) {
                self::assertSame(ErrorCode::ColumnsWidthInvalid, $exception->errorCode());
            }
        }
    }

    /**
     * @param list<mixed> $arguments
     */
    #[DataProvider('invalidColumns')]
    public function testInvalidColumns(array $arguments, ErrorCode $code): void
    {
        try {
            (new ReflectionClass(Columns::class))->newInstanceArgs($arguments);
            self::fail('Invalid columns were accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{list<mixed>, ErrorCode}>
     */
    public static function invalidColumns(): iterable
    {
        yield 'first map' => [[['named' => new Text('Text')], []], ErrorCode::ColumnsChildrenInvalid];
        yield 'second map' => [[[], [1 => new Text('Text')]], ErrorCode::ColumnsChildrenInvalid];
        yield 'first non-component' => [[['Text'], []], ErrorCode::ColumnsChildrenInvalid];
        yield 'second non-component' => [[[], ['Text']], ErrorCode::ColumnsChildrenInvalid];
        yield 'negative gap' => [[[], [], -1], ErrorCode::ValueOutOfRange];
        yield 'large gap' => [[[], [], 65], ErrorCode::ValueOutOfRange];
    }
}
