<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders fluid stacking columns with exact-width Outlook table cells
 *
 * @implements HtmlComponentRendererInterface<Columns>
 *
 * @internal
 */
final readonly class ColumnsRenderer implements HtmlComponentRendererInterface
{
    /**
     * Splits available width between nested columns and supplies mobile and Outlook layout
     *
     * @param Columns $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): ?RenderedComponent {
        if ($component->first() === [] && $component->second() === []) {
            return null;
        }
        $gap = $component->gap();
        $availableWidth = $contentWidth - $gap;
        if ($availableWidth < 2) {
            throw new InvalidInputException(ErrorCode::ColumnsWidthInvalid, 'Columns need at least one pixel per column after subtracting the gap.');
        }
        $firstWidth = $component->firstWidth() ?? intdiv($availableWidth, 2);
        if ($firstWidth >= $availableWidth) {
            throw new InvalidInputException(ErrorCode::ColumnsWidthInvalid, 'The first column width leaves no room for the second column.');
        }
        $secondWidth = $availableWidth - $firstWidth;
        $firstRows = $dispatcher->renderComponents($component->first(), $firstWidth, true);
        $secondRows = $dispatcher->renderComponents($component->second(), $secondWidth, true);
        if ($firstRows === '' && $secondRows === '') {
            return null;
        }
        $first = $this->column($firstRows, $firstWidth, 'email-column-first');
        $second = $this->column($secondRows, $secondWidth, 'email-column-second');
        $gutter = '<div class="email-column-gap-' . $gap . '" aria-hidden="true" style="display:inline-block;vertical-align:top;width:'
            . $gap . 'px;height:0;font-size:0;line-height:0;">&nbsp;</div>';
        $mobileClass = '';
        $mobileStyle = '';
        if ($component->mobileFirstWidth() !== null) {
            $mobileWidth = $component->mobileFirstWidth();
            $mobileClass = ' email-columns-mobile-first-' . $mobileWidth;
            $selector = '.email-columns-mobile-first-' . $mobileWidth;
            $mobileStyle = $selector . '{display:table!important;width:100%!important;}'
                . $selector . ' .email-column{display:table-cell!important;vertical-align:top!important;}'
                . $selector . ' .email-column-first{width:' . $mobileWidth . 'px!important;max-width:' . $mobileWidth . 'px!important;}'
                . $selector . ' .email-column-second{width:auto!important;max-width:none!important;}'
                . $selector . ' .email-column-gap-' . $gap . '{display:table-cell!important;width:' . $gap . 'px!important;height:auto!important;}'
                . $selector . ' .email-column-first img{width:' . $mobileWidth . 'px!important;max-width:' . $mobileWidth . 'px!important;height:auto!important;}';
        }

        return new RenderedComponent('<style type="text/css">@media only screen and (max-width:600px){'
            . '.email-column{display:block!important;max-width:100%!important;}'
            . '.email-column-gap-' . $gap . '{display:block!important;width:100%!important;height:' . $gap . 'px!important;}'
            . $mobileStyle . '}</style>'
            . '<div class="email-columns' . $mobileClass . '" style="font-size:0;line-height:0;">'
            . '<!--[if mso]><table role="presentation" width="' . $contentWidth . '" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;"><tr>'
            . '<td width="' . $firstWidth . '" valign="top"><![endif]-->' . $first
            . '<!--[if mso]></td><td width="' . $gap . '" aria-hidden="true" style="font-size:0;line-height:0;"><![endif]-->' . $gutter
            . '<!--[if mso]></td><td width="' . $secondWidth . '" valign="top"><![endif]-->' . $second
            . '<!--[if mso]></td></tr></table><![endif]--></div>', spacingAfter: 24);
    }

    /**
     * Wraps child rows in a fluid column bounded by its desktop width
     */
    private function column(string $rows, int $width, string $class): string
    {
        return '<div class="email-column ' . $class . '" style="display:inline-block;vertical-align:top;width:100%;max-width:' . $width . 'px;">'
            . '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;table-layout:fixed;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . $rows . '</table></div>';
    }
}
