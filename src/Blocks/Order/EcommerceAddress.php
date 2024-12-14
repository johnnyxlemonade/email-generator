<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Address;

class EcommerceAddress extends AbstractBlock
{
    /**
     * Constructor for `EcommerceAddress`.
     *
     * @param Address $billingAddress Billing address.
     * @param Address $deliveryAddress Delivery address.
     */
    public function __construct(Address $billingAddress, Address $deliveryAddress)
    {
        // Initialize context
        $context = new ContextData();

        // Add addresses to context
        $context->set("billingAddress", $billingAddress);
        $context->set("deliveryAddress", $deliveryAddress);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Assume `address` is already set in the context
        $this->context->validate(["billingAddress", "deliveryAddress"]);
    }
}
