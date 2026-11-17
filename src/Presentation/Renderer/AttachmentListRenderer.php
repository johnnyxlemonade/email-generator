<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Renders attachment metadata as one compact table with divided rows
 *
 * @implements HtmlComponentRendererInterface<AttachmentList>
 *
 * @internal
 */
final readonly class AttachmentListRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders a localized heading and linked or plain filenames without inter-row component spacing
     *
     * @param AttachmentList $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): ?RenderedComponent {
        if ($component->items() === []) {
            return null;
        }

        $fontFamily = $context->config()->fontFamily()->css();
        $heading = $context->text(new Translation(
            count($component->items()) === 1
                ? 'email_generator.attachment_list.heading_single'
                : 'email_generator.attachment_list.heading_plural',
            ['count' => count($component->items())],
        ));
        $rows = '';
        $lastIndex = count($component->items()) - 1;

        foreach ($component->items() as $index => $item) {
            $rows .= $this->row(
                item: $item,
                context: $context,
                fontFamily: $fontFamily,
                divided: $index !== $lastIndex,
            );
        }

        $html = '<table role="presentation" width="' . $contentWidth . '" border="0" cellpadding="0" cellspacing="0" '
            . 'style="width:100%;max-width:' . $contentWidth . 'px;border:1px solid #cbd5e1;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . '<tr><td colspan="2" style="padding:14px 16px 12px;border-bottom:1px solid #cbd5e1;">'
            . '<h2 style="margin:0;color:' . $context->config()->textColor()->hex() . ';font-family:' . $fontFamily
            . ';font-size:22px;line-height:30px;font-weight:bold;">' . $heading . '</h2></td></tr>'
            . $rows . '</table>';

        return new RenderedComponent($html);
    }

    /**
     * Renders one file row with a format label, prominent filename and optional size
     */
    private function row(
        AttachmentItem $item,
        RenderContext $context,
        string $fontFamily,
        bool $divided,
    ): string {
        $border = $divided ? 'border-bottom:1px solid #e2e8f0;' : '';
        $filename = '<strong style="font-weight:bold;">' . $context->text($item->filename()) . '</strong>';

        if ($item->url() !== null) {
            $filename = '<a href="' . RenderContext::escape($item->url()) . '" style="color:'
                . $context->config()->accentColor()->hex() . ';text-decoration:underline;">' . $filename . '</a>';
        }

        $size = $item->bytes() === null
            ? ''
            : '<span style="display:block;margin-top:2px;color:#64748b;font-family:' . $fontFamily
                . ';font-size:13px;line-height:18px;">' . $this->formatBytes($item->bytes()) . '</span>';

        return '<tr><td width="72" valign="middle" style="width:72px;padding:12px 8px 12px 16px;' . $border . '">'
            . '<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . '<tr><td bgcolor="#f1f5f9" style="padding:3px 8px;border-radius:4px;background-color:#f1f5f9;color:#334155;font-family:'
            . $fontFamily . ';font-size:12px;font-weight:bold;line-height:18px;">'
            . RenderContext::escape($item->type()) . '</td></tr></table></td>'
            . '<td valign="middle" style="padding:12px 16px 12px 8px;' . $border . 'color:'
            . $context->config()->textColor()->hex() . ';font-family:' . $fontFamily
            . ';font-size:16px;line-height:22px;word-wrap:break-word;">' . $filename . $size . '</td></tr>';
    }

    /**
     * Formats a non-negative byte count using decimal file-size units
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $value = (float) $bytes;
        $unit = 0;

        while ($value >= 1000 && $unit < count($units) - 1) {
            $value /= 1000;
            ++$unit;
        }

        $precision = $unit > 0 && $value < 10 && floor($value) !== $value ? 1 : 0;

        return number_format($value, $precision, '.', '') . ' ' . $units[$unit];
    }
}
