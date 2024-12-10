<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\CouponCollection;
use Lemonade\EmailGenerator\Context\ContextData;

/**
 * Class EcommerceCoupon
 * Represents a block component for displaying a list of coupons.
 */
class EcommerceCoupon extends AbstractBlock
{

    /**
     * CouponList constructor.
     * Initializes the block with a collection of coupons.
     *
     * @param CouponCollection $collection A collection of coupons to be used in the block.
     * @param string $currency A currency symbol for collection of coupons
     */
    public function __construct(CouponCollection $collection, string $currency)
    {
        // Initialize context
        $context = new ContextData();
        $context->set("coupons", $collection->all());
        $context->set("currency", $currency);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the context for the block.
     *
     * Ensures that the required data (in this case, "coupons", "currency") is present in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        $this->context->validate(["coupons", "currency"]);
    }
}