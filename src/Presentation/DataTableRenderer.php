<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation;

use Lemonade\EmailGenerator\Component\Table;
use Lemonade\EmailGenerator\Presentation\Renderer\RichTextRenderer;

/**
 * Renders semantic data tables with shared inline text formatting
 *
 * @internal
 */
final readonly class DataTableRenderer
{
    /**
     * Renders headers and cells without a presentation-table role
     */
    public static function render(Table $table, RenderContext $context): string
    {
        $style = 'padding:10px;border:1px solid #e2e8f0;color:' . $context->config()->textColor()->hex()
            . ';font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:20px;vertical-align:top;word-wrap:break-word;';
        $html = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;table-layout:fixed;mso-table-lspace:0pt;mso-table-rspace:0pt;">';
        if ($table->headers() !== []) {
            $html .= '<thead><tr>';
            foreach ($table->headers() as $header) {
                $html .= '<th scope="col" style="' . $style . 'font-weight:bold;">' . RichTextRenderer::render($header, $context) . '</th>';
            }
            $html .= '</tr></thead>';
        }
        $html .= '<tbody>';
        foreach ($table->rows() as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td style="' . $style . '">' . RichTextRenderer::render($cell, $context) . '</td>';
            }
            $html .= '</tr>';
        }

        return $html . '</tbody></table>';
    }
}
