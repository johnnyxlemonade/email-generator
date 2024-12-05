<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\Models\Shipping;

class EcommerceDelivery extends AbstractBlock
{
    /**
     * Constructor for `EcommerceDelivery`.
     *
     * @param Shipping $shipping Information about the shipping method.
     * @param Payment $payment Information about the payment method.
     * @param string $currency Currency code.
     */
    public function __construct(Shipping $shipping, Payment $payment, string $currency)
    {
        // Initialize context
        $context = new ContextData();

        // Add shipping and payment information to context
        $context->set("shipping", $shipping);
        $context->set("payment", $payment);
        $context->set("currency", $currency);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        $this->context->validate(["shipping", "payment", "currency"]);
    }
}