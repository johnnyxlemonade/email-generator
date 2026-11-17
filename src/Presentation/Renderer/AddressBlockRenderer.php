<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Renders compact localized company and postal details
 *
 * @implements HtmlComponentRendererInterface<AddressBlock>
 *
 * @internal
 */
final readonly class AddressBlockRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders required address lines and only the present optional details
     *
     * @param AddressBlock $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        $address = $component->address();
        $html = '<p class="email-address" style="margin:0;color:#475569;font-family:' . $context->config()->fontFamily()->css() . ';font-size:13px;line-height:19px;">'
            . '<strong style="color:' . $context->config()->textColor()->hex() . ';font-weight:bold;">' . $context->text($address->name()) . '</strong>'
            . '<br>' . $context->text($address->street())
            . '<br>' . $context->text($address->city())
            . '<br>' . $context->text($address->postalCode())
            . '<br>' . $context->text($address->country());
        foreach ([
            ['email_generator.address_block.company_id', $address->companyId()],
            ['email_generator.address_block.tax_id', $address->taxId()],
            ['email_generator.address_block.email', $address->email()],
            ['email_generator.address_block.phone', $address->phone()],
        ] as [$label, $value]) {
            if ($value !== null) {
                $html .= '<br>' . $context->text(new Translation($label)) . ': ' . RenderContext::escape($value);
            }
        }

        return new RenderedComponent($html . '</p>');
    }
}
