<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Collection\AbstractCollection;

class EcommerceProductList extends AbstractBlock
{
    /**
     * Constructor for `EcommerceProductList`.
     *
     * @param AbstractCollection $collection Collection of products.
     * @param string $currency The currency used for displaying product prices.
     */
    public function __construct(AbstractCollection $collection, string $currency)
    {
        // Initialize the context with product data and currency
        $context = new ContextData();
        $context->set("products", $collection->all());
        $context->set("currency", $currency);

        // Pass the context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validate the context for the product list.
     *
     * Ensures that the essential keys are set in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        $this->context->validate(["products", "currency"]);
    }
}

