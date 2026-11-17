<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Arranges existing button rendering horizontally with a stacking mobile layout
 *
 * @implements HtmlComponentRendererInterface<ButtonGroup>
 *
 * @internal
 */
final readonly class ButtonGroupRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders button content directly without the dispatcher's normal row wrappers
     *
     * @param ButtonGroup $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        $gap = $component->gap();
        $lastIndex = count($component->buttons()) - 1;
        $items = '';
        foreach ($component->buttons() as $index => $button) {
            $rendered = $dispatcher->renderContent($button, $contentWidth);
            $rightGap = $index === $lastIndex ? 0 : $gap;
            $items .= '<!--[if mso]><td valign="top" style="padding-right:' . $rightGap . 'px;"><![endif]-->'
                . '<div class="email-button-group-item" style="display:inline-block;vertical-align:top;margin-right:' . $rightGap . 'px;">'
                . $rendered?->html() . '</div><!--[if mso]></td><![endif]-->';
        }

        return new RenderedComponent('<style type="text/css">@media only screen and (max-width:600px){'
            . '.email-button-group-item{display:block!important;width:100%!important;margin-right:0!important;margin-bottom:' . $gap . 'px!important;}'
            . '.email-button-group-item:last-child{margin-bottom:0!important;}'
            . '.email-button-group-item table{width:100%!important;}.email-button-group-item a{display:block!important;}}</style>'
            . '<div class="email-button-group" style="font-size:0;line-height:0;text-align:' . $component->alignment()->value . ';">'
            . '<!--[if mso]><table role="presentation" align="' . $component->alignment()->value
            . '" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;"><tr><![endif]-->'
            . $items . '<!--[if mso]></tr></table><![endif]--></div>', spacingAfter: 24);
    }
}
