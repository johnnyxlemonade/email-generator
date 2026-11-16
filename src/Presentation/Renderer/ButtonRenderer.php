<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Button content without its outer layout row
 *
 * @internal
 */
final readonly class ButtonRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders an escaped button link using the configured accent and label colors
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        int $contentWidth,
        ComponentRenderer $dispatcher,
    ): RenderedComponent {
        if (!$component instanceof Button) {
            throw new CompositionException(ErrorCode::ComponentUnsupported, 'ButtonRenderer requires Button.');
        }
        $config = $context->config();

        return new RenderedComponent('<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . '<tr><td align="center" bgcolor="' . $config->accentColor()->hex() . '" style="background-color:' . $config->accentColor()->hex() . ';border-radius:4px;">'
            . '<a href="' . RenderContext::escape($component->url()) . '" style="display:inline-block;border-style:solid;border-width:12px 20px;border-color:'
            . $config->accentColor()->hex() . ';border-radius:4px;color:' . $config->buttonTextColor()->hex()
            . ';font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;line-height:24px;text-align:center;text-decoration:none;mso-line-height-rule:exactly;">'
            . $context->text($component->label()) . '</a></td></tr></table>');
    }
}
