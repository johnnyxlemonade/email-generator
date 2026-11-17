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
        $style = 'padding:10px;border:1px solid #e2e8f0;color:' . $context->config()->textColor()->hex()
            . ';font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:20px;vertical-align:top;word-wrap:break-word;';
        $html = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;table-layout:fixed;mso-table-lspace:0pt;mso-table-rspace:0pt;">';
        if ($component->headers() !== []) {
            $html .= '<thead><tr>';
            foreach ($component->headers() as $header) {
                $html .= '<th scope="col" style="' . $style . 'font-weight:bold;">' . RichTextRenderer::render($header, $context) . '</th>';
            }
            $html .= '</tr></thead>';
        }
        $html .= '<tbody>';
        foreach ($component->rows() as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td style="' . $style . '">' . RichTextRenderer::render($cell, $context) . '</td>';
            }
            $html .= '</tr>';
        }

        return new RenderedComponent($html . '</tbody></table>');
    }
}
