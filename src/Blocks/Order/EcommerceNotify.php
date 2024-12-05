<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EcommerceNotify extends AbstractBlock
{
    /**
     * Constructor for `EcommerceNotify`.
     *
     * @param ContextData $context Context data to initialize the block with.
     */
    public function __construct(ContextData $context)
    {
        // Call the parent constructor with the given context
        parent::__construct($context);
    }

    /**
     * Validate the required keys in the context.
     *
     * Ensures that the essential keys are present in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // List of required keys for this block
        $this->validateRequiredKeys(["webName"]);
    }
}
