<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders a compact table-backed label using a fixed status palette
 *
 * @implements HtmlComponentRendererInterface<Badge>
 *
 * @internal
 */
final readonly class BadgeRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders an escaped status label in a compact table with fixed variant colors
     *
     * @param Badge $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        [$background, $foreground] = match ($component->variant()) {
            BadgeVariant::Neutral => ['#f1f5f9', '#334155'],
            BadgeVariant::Info => ['#dbeafe', '#1e40af'],
            BadgeVariant::Success => ['#dcfce7', '#166534'],
            BadgeVariant::Warning => ['#fef3c7', '#92400e'],
            BadgeVariant::Danger => ['#fee2e2', '#991b1b'],
        };

        $html = '<table class="email-badge" role="presentation" border="0" cellpadding="0" cellspacing="0" '
            . 'style="max-width:100%;border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . '<tr><td bgcolor="' . $background . '" style="padding:4px 8px;border-radius:4px;background-color:' . $background
            . ';color:' . $foreground . ';font-family:' . $context->config()->fontFamily()->css()
            . ';font-size:12px;font-weight:bold;line-height:18px;word-wrap:break-word;">'
            . $context->text($component->label()) . '</td></tr></table>';

        return new RenderedComponent($html, spacingAfter: 12);
    }
}
