<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\Models\Shipping;
use Lemonade\EmailGenerator\Services\ContextService;

class EcommerceDelivery extends AbstractBlock
{
    /**
     * Constructor for `EcommerceDelivery`.
     *
     * @param ContextService $contextService Context Service.
     * @param Shipping $shipping Information about the shipping method.
     * @param Payment $payment Information about the payment method.
     * @param string $currency Currency code.
     */
    public function __construct(protected readonly ContextService $contextService, Shipping $shipping, Payment $payment, string $currency)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "shipping" => $shipping,
            "payment" => $payment,
            "currency" => $currency
        ]);

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