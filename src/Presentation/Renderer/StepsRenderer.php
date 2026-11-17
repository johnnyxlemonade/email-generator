<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\StepsProgress;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders an accessible ordered process as an Outlook-safe responsive table
 *
 * @implements HtmlComponentRendererInterface<Steps>
 *
 * @internal
 */
final readonly class StepsRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders equal desktop cells and visible state labels that stack on mobile
     *
     * @param Steps $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        $count = count($component->steps());
        if ($contentWidth < $count) {
            throw new InvalidInputException(ErrorCode::StepsWidthInvalid, 'Steps need at least one available pixel per step.');
        }
        $baseWidth = intdiv($contentWidth, $count);
        $remainingWidth = $contentWidth - $baseWidth * $count;
        $cells = '';
        foreach ($component->steps() as $index => $name) {
            $position = $index + 1;
            $state = $this->state($component, $position);
            $stateLabel = match ($state) {
                'completed' => $component->completedLabel(),
                'current' => $component->currentLabel(),
                'pending' => $component->pendingLabel(),
            };
            $width = $baseWidth + ($index === $count - 1 ? $remainingWidth : 0);
            $color = $state === 'pending' ? '#cbd5e1' : $context->config()->accentColor()->hex();
            $current = $state === 'current' ? ' aria-current="step"' : '';
            $nameStyle = $state === 'current' ? 'font-weight:bold;' : ($state === 'completed' ? 'text-decoration:line-through;' : '');
            $cells .= '<td class="email-step" role="listitem"' . $current . ' width="' . $width
                . '" valign="top" style="width:' . $width . 'px;padding:10px 8px 8px;border-top:4px solid ' . $color
                . ';font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:20px;text-align:left;vertical-align:top;">'
                . '<span style="display:block;color:' . $context->config()->textColor()->hex() . ';' . $nameStyle . '">'
                . $position . '. ' . $context->text($name) . '</span>'
                . '<span style="display:block;color:' . $context->config()->textColor()->hex() . ';font-size:12px;line-height:18px;">'
                . $context->text($stateLabel) . '</span></td>';
        }

        return new RenderedComponent('<style type="text/css">@media only screen and (max-width:600px){'
            . '.email-steps,.email-steps tbody,.email-steps tr{display:block!important;width:100%!important;}'
            . '.email-step{display:block!important;width:auto!important;margin-bottom:12px!important;}'
            . '.email-step:last-child{margin-bottom:0!important;}}</style>'
            . '<table class="email-steps" role="list" width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;table-layout:fixed;border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;"><tr>'
            . $cells . '</tr></table>');
    }

    /**
     * Derives a stable completed, current or pending state for one position
     *
     * @return 'completed'|'current'|'pending'
     */
    private function state(Steps $component, int $position): string
    {
        if ($component->progress() === StepsProgress::NotStarted) {
            return 'pending';
        }
        if ($component->progress() === StepsProgress::Completed) {
            return 'completed';
        }
        if ($position === $component->currentStep()) {
            return 'current';
        }

        return $position < $component->currentStep() ? 'completed' : 'pending';
    }
}
