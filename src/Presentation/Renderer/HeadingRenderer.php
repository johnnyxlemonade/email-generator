<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Heading content without its outer layout row
 *
 * @internal
 */
final readonly class HeadingRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders safe inline content with the element and font size for its heading level
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        int $contentWidth,
        ComponentRenderer $dispatcher,
    ): RenderedComponent {
        if (!$component instanceof Heading) {
            throw new CompositionException(ErrorCode::ComponentUnsupported, 'HeadingRenderer requires Heading.');
        }
        $size = [1 => 28, 2 => 24, 3 => 20, 4 => 18, 5 => 16, 6 => 14][$component->level()];

        return new RenderedComponent('<h' . $component->level() . ' style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:'
            . $size . 'px;line-height:' . ($size + 8) . 'px;font-weight:bold;">'
            . RichTextRenderer::render($component->content(), $context) . '</h' . $component->level() . '>');
    }
}
