<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EcommerceHeader extends AbstractBlock
{
    /**
     * Constructor for `EcommerceHeader`.
     *
     * @param ContextData $context The context data for the block.
     */
    public function __construct(ContextData $context)
    {
        // Call the parent constructor with the context
        parent::__construct($context);
    }

    /**
     * Validate the required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validation of expected keys in the context
        $this->context->validate(["orderId", "orderCode", "orderTotal", "orderCurrency", "orderDate"]);
    }
}
