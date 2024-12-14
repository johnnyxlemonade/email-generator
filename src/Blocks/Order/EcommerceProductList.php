<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Collection\AbstractCollection;
use Lemonade\EmailGenerator\Services\ContextService;

class EcommerceProductList extends AbstractBlock
{
    /**
     * Constructor for `EcommerceProductList`.
     *
     * @param ContextService $contextService Context Service.
     * @param AbstractCollection $collection Collection of products.
     * @param string $currency The currency used for displaying product prices.
     */
    public function __construct(protected readonly ContextService $contextService, AbstractCollection $collection, string $currency)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "products" => $collection->all(),
            "currency" => $currency,
        ]);

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

