<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\CouponCollection;
use Lemonade\EmailGenerator\Services\ContextService;

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
     * @param ContextService $contextService Context Service.
     * @param CouponCollection $collection A collection of coupons to be used in the block.
     */
    public function __construct(protected readonly ContextService $contextService, CouponCollection $collection)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "coupons" => $collection->all()
        ]);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the context for the block.
     *
     * Ensures that the required data (in this case, "coupons") is present in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        $this->context->validate(["coupons"]);
    }
}