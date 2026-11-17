<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Group content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<Group>
 *
 * @internal
 */
final readonly class GroupRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders child rows within the group padding and border, omitting an empty group
     *
     * @param Group $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): ?RenderedComponent {
        if ($component->children() === []) {
            return null;
        }

        $width = $contentWidth - 2 * $component->padding() - ($component->borderColor() === null ? 0 : 2);

        if ($width < 1) {
            throw new InvalidInputException(ErrorCode::GroupWidthInvalid, 'Group padding and borders leave no room for content.');
        }

        $rows = $dispatcher->renderComponents($component->children(), $width, true);
        $background = $component->backgroundColor() === null ? '' : ' bgcolor="' . $component->backgroundColor()->hex() . '"';
        $style = 'padding:' . $component->padding() . 'px;';

        if ($component->backgroundColor() !== null) {
            $style .= 'background-color:' . $component->backgroundColor()->hex() . ';';
        }
        if ($component->borderColor() !== null) {
            $style .= 'border:1px solid ' . $component->borderColor()->hex() . ';';
        }

        $html = '<table class="email-group" role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" '
            . 'style="width:100%;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . '<tr><td' . $background . ' style="' . $style . '">'
            . '<table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;">'
            . $rows . '</table></td></tr></table>';

        return new RenderedComponent($html, spacingAfter: 24);
    }
}
