<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Image content without its outer layout row
 *
 * @internal
 */
final readonly class ImageRenderer implements HtmlComponentRendererInterface
{
    /**
     * Constrains image dimensions to available width while preserving the requested aspect ratio
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        int $contentWidth,
        ComponentRenderer $dispatcher,
    ): RenderedComponent {
        if (!$component instanceof Image) {
            throw new CompositionException(ErrorCode::ComponentUnsupported, 'ImageRenderer requires Image.');
        }
        // Clamp to the shell's inner width, including proportional HTML dimensions for Outlook.
        $width = min($component->width(), $contentWidth);
        $height = $component->height() === null ? '' : ' height="' . max(1, (int) round($component->height() * $width / $component->width())) . '"';

        return new RenderedComponent('<img src="' . RenderContext::escape($component->url()) . '" alt="' . $context->text($component->alt())
            . '" width="' . $width . '"' . $height
            . ' border="0" style="display:block;width:100%;max-width:' . $width . 'px;height:auto;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;">');
    }
}
