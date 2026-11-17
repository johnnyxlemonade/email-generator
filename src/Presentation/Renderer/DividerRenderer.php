<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Divider;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Divider content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<Divider>
 *
 * @internal
 */
final readonly class DividerRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders a full-width separator with the requested color and thickness
     *
     * @param Divider $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        return new RenderedComponent('<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . '<tr><td height="' . $component->thickness() . '" bgcolor="' . $component->color()->hex() . '" aria-hidden="true" style="height:'
            . $component->thickness() . 'px;background-color:' . $component->color()->hex() . ';font-size:1px;line-height:'
            . $component->thickness() . 'px;mso-line-height-rule:exactly;">&nbsp;</td></tr></table>', spacingAfter: 20);
    }
}
