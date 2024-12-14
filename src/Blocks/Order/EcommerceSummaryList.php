<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Services\ContextService;

class EcommerceSummaryList extends AbstractBlock
{
    /**
     * Constructor for `EcommerceSummaryList`.
     *
     * @param ContextService $contextData Context Service.
     * @param SummaryCollection $collection Collection of order summaries.
     * @param string $currency The currency for displaying the summary values.
     */
    public function __construct(protected readonly ContextService $contextService, SummaryCollection $collection, string $currency)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "summary" => $collection->all(),
            "currency" => $currency,
        ]);

        // Pass the context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validate the context for the order summary.
     *
     * Ensures that the basic keys are set in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that certain essential keys are present in the context
        $this->context->validate(["summary", "currency"]);
    }
}
