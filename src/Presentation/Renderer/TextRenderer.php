<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Text content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<Text>
 *
 * @internal
 */
final readonly class TextRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders escaped paragraph content with supported inline formatting and line breaks
     *
     * @param Text $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        $alignment = $component->alignment();
        $attribute = $alignment === HorizontalAlignment::Left ? '' : ' align="' . $alignment->value . '"';
        $style = $alignment === HorizontalAlignment::Left ? '' : 'text-align:' . $alignment->value . ';';

        return new RenderedComponent('<p' . $attribute . ' style="margin:0;' . $style . '">'
            . RichTextRenderer::render($component->content(), $context) . '</p>');
    }
}
