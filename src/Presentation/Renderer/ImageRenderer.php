<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Image content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<Image>
 *
 * @internal
 */
final readonly class ImageRenderer implements HtmlComponentRendererInterface
{
    /**
     * Constrains image dimensions to available width while preserving the requested aspect ratio
     *
     * @param Image $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        // Clamp to the shell's inner width, including proportional HTML dimensions for Outlook.
        $width = min($component->width(), $contentWidth);
        $height = $component->height() === null ? '' : ' height="' . max(1, (int) round($component->height() * $width / $component->width())) . '"';

        return new RenderedComponent('<img src="' . RenderContext::escape($component->url()) . '" alt="' . $context->text($component->alt())
            . '" width="' . $width . '"' . $height
            . ' border="0" style="display:block;width:100%;max-width:' . $width . 'px;height:auto;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;">');
    }
}
