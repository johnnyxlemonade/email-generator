<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Heading content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<Heading>
 *
 * @internal
 */
final readonly class HeadingRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders safe inline content with the element and font size for its heading level
     *
     * @param Heading $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        $size = [1 => 28, 2 => 22, 3 => 18, 4 => 16, 5 => 14, 6 => 13][$component->level()];

        return new RenderedComponent('<h' . $component->level() . ' style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:'
            . $size . 'px;line-height:' . ($size + 8) . 'px;font-weight:bold;">'
            . RichTextRenderer::render($component->content(), $context) . '</h' . $component->level() . '>', spacingAfter: 12);
    }
}
