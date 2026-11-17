<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
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
        return new RenderedComponent('<p style="margin:0;">' . RichTextRenderer::render($component->content(), $context) . '</p>');
    }
}
