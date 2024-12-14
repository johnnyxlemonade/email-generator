<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\FormItemCollection;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Services\ContextService;

/**
 * Class ComponentFormItemList
 * Represents a block component for managing and displaying a list of form items.
 */
class ComponentFormItemList extends AbstractBlock
{
    /**
     * Constructor for `ComponentFormItemList`.
     *
     * Initializes the block with a name and a collection of form items.
     *
     * @param ContextService $contextData Context Service.
     * @param string $name The name of the form list.
     * @param FormItemCollection $collection The collection of form items.
     */
    public function __construct(protected readonly ContextService $contextService, string $name, FormItemCollection $collection)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "name" => $name,
            "formlist" => $collection->all()
        ]);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the required keys in the context.
     *
     * Ensures that both "name" and "formlist" keys are present in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that "name" and "formlist" keys are present in the context
        $this->context->validate(["name", "formlist"]);
    }
}
