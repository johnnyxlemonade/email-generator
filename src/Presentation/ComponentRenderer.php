<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation;

use Lemonade\EmailGenerator\Component\{
    AddressBlock,
    AttachmentList,
    Badge,
    BulletList,
    Button,
    ButtonGroup,
    Columns,
    Divider,
    Group,
    Heading,
    Image,
    Spacer,
    Steps,
    Table,
    Text,
};
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\Renderer\{
    AddressBlockRenderer,
    AttachmentListRenderer,
    BadgeRenderer,
    BulletListRenderer,
    ButtonGroupRenderer,
    ButtonRenderer,
    ColumnsRenderer,
    DividerRenderer,
    GroupRenderer,
    HeadingRenderer,
    ImageRenderer,
    SpacerRenderer,
    StepsRenderer,
    TableRenderer,
    TextRenderer,
};

/**
 * Dispatches component rendering and applies shared row layout and spacing
 *
 * @internal
 */
final readonly class ComponentRenderer
{
    private int $contentWidth;

    /**
     * @var array<class-string<ComponentInterface>, object>
     */
    private array $renderers;

    /**
     * Registers the built-in renderers for this render's isolated context
     *
     * @param array<class-string<ComponentInterface>, HtmlComponentRendererInterface<*>>|null $renderers
     */
    public function __construct(
        private RenderContext $context,
        ?int $contentWidth = null,
        ?array $renderers = null,
    ) {
        $this->contentWidth = $contentWidth ?? $context->config()->width()->value - 64;
        $this->renderers = $renderers ?? [
            AddressBlock::class => new AddressBlockRenderer(),
            AttachmentList::class => new AttachmentListRenderer(),
            Heading::class => new HeadingRenderer(),
            Text::class => new TextRenderer(),
            Image::class => new ImageRenderer(),
            Button::class => new ButtonRenderer(),
            BulletList::class => new BulletListRenderer(),
            Table::class => new TableRenderer(),
            Divider::class => new DividerRenderer(),
            Spacer::class => new SpacerRenderer(),
            Group::class => new GroupRenderer(),
            Columns::class => new ColumnsRenderer(),
            Badge::class => new BadgeRenderer(),
            ButtonGroup::class => new ButtonGroupRenderer(),
            Steps::class => new StepsRenderer(),
        ];
    }

    /**
     * Renders a single component with its shared layout row
     */
    public function render(ComponentInterface $component, bool $last = false): string
    {
        $content = $this->dispatch($component, $this->contentWidth);

        return $content === null ? '' : $this->row($content, $last);
    }

    /**
     * Renders visible components in order, optionally removing the final normal gap
     *
     * @param list<ComponentInterface> $components
     */
    public function renderComponents(
        array $components,
        ?int $contentWidth = null,
        bool $trimTrailingSpacing = false,
    ): string {
        $rendered = [];

        foreach ($components as $component) {
            $content = $this->dispatch($component, $contentWidth ?? $this->contentWidth);

            if ($content !== null) {
                $rendered[] = $content;
            }
        }

        $rows = '';
        $lastIndex = count($rendered) - 1;

        foreach ($rendered as $index => $content) {
            $rows .= $this->row($content, $trimTrailingSpacing && $index === $lastIndex);
        }

        return $rows;
    }

    /**
     * Renders nested component content without adding a shared table row or spacing
     */
    public function renderContent(ComponentInterface $component, int $contentWidth): ?RenderedComponent
    {
        return $this->dispatch($component, $contentWidth);
    }

    /**
     * Selects a renderer by exact component class and forwards the available width
     */
    private function dispatch(ComponentInterface $component, int $contentWidth): ?RenderedComponent
    {
        $registeredRenderer = $this->renderers[$component::class] ?? throw new CompositionException(
            ErrorCode::ComponentUnsupported,
            'Document contains an unexpanded or unsupported component.',
        );

        /** @var HtmlComponentRendererInterface<ComponentInterface> $renderer */
        $renderer = $registeredRenderer;

        return $renderer->render($component, $this->context, $this, $contentWidth);
    }

    /**
     * Applies either fixed-height row sizing or the shared text layout and spacing
     */
    private function row(RenderedComponent $content, bool $last): string
    {
        if ($content->height() !== null) {
            return '<tr><td height="' . $content->height() . '" aria-hidden="true" style="height:' . $content->height()
                . 'px;font-size:1px;line-height:' . $content->height() . 'px;mso-line-height-rule:exactly;">'
                . $content->html() . '</td></tr>' . "\n";
        }

        $spacing = $last ? 0 : $content->spacingAfter();

        return '<tr><td style="padding:0 0 ' . $spacing . 'px;color:' . $this->context->config()->textColor()->hex()
            . ';font-family:' . $this->context->config()->fontFamily()->css() . ';font-size:16px;line-height:24px;word-wrap:break-word;">'
            . $content->html() . '</td></tr>' . "\n";
    }
}
