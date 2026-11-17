<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Table;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Table content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<Table>
 *
 * @internal
 */
final readonly class TableRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders semantic headers and cells with shared inline text formatting
     *
     * @param Table $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        if ($component->compact()) {
            return $this->renderCompact($component, $context);
        }
        $style = 'padding:10px;border:1px solid #e2e8f0;color:' . $context->config()->textColor()->hex()
            . ';font-family:' . $context->config()->fontFamily()->css() . ';font-size:14px;line-height:20px;vertical-align:top;word-wrap:break-word;';
        $html = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;table-layout:fixed;mso-table-lspace:0pt;mso-table-rspace:0pt;">';
        if ($component->headers() !== []) {
            $html .= '<thead><tr>';
            foreach ($component->headers() as $column => $header) {
                $alignment = $component->alignmentForColumn($column)->value;
                $html .= '<th scope="col" align="' . $alignment . '" style="' . $style . 'text-align:' . $alignment
                    . ';font-weight:bold;">' . RichTextRenderer::render($header, $context) . '</th>';
            }
            $html .= '</tr></thead>';
        }
        $html .= '<tbody>';
        foreach ($component->rows() as $row) {
            $html .= '<tr>';
            foreach ($row as $column => $cell) {
                $alignment = $component->alignmentForColumn($column)->value;
                $html .= '<td align="' . $alignment . '" style="' . $style . 'text-align:' . $alignment . ';">'
                    . RichTextRenderer::render($cell, $context) . '</td>';
            }
            $html .= '</tr>';
        }

        return new RenderedComponent($html . '</tbody></table>', spacingAfter: 24);
    }

    /**
     * Renders label-value rows without the visual weight of a data grid
     */
    private function renderCompact(Table $component, RenderContext $context): RenderedComponent
    {
        $html = '<table class="email-key-value" width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;table-layout:fixed;mso-table-lspace:0pt;mso-table-rspace:0pt;"><tbody>';
        $lastIndex = count($component->rows()) - 1;
        foreach ($component->rows() as $index => $row) {
            $border = $index === $lastIndex ? '' : 'border-bottom:1px solid #e2e8f0;';
            $labelAlignment = $component->alignmentForColumn(0)->value;
            $valueAlignment = $component->alignmentForColumn(1)->value;
            $html .= '<tr><td width="38%" align="' . $labelAlignment . '" valign="top" style="width:38%;padding:8px 12px 8px 0;' . $border
                . 'text-align:' . $labelAlignment . ';'
                . 'color:#64748b;font-family:' . $context->config()->fontFamily()->css() . ';font-size:13px;line-height:20px;vertical-align:top;word-wrap:break-word;">'
                . RichTextRenderer::render($row[0], $context) . '</td>'
                . '<td align="' . $valueAlignment . '" valign="top" style="padding:8px 0 8px 12px;' . $border
                . 'text-align:' . $valueAlignment . ';color:' . $context->config()->textColor()->hex()
                . ';font-family:' . $context->config()->fontFamily()->css() . ';font-size:14px;line-height:20px;vertical-align:top;word-wrap:break-word;">'
                . RichTextRenderer::render($row[1], $context) . '</td></tr>';
        }

        return new RenderedComponent($html . '</tbody></table>', spacingAfter: 24);
    }
}
