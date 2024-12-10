<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\FormItemCollection;
use Lemonade\EmailGenerator\Context\ContextData;

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
     * @param string $name The name of the form list.
     * @param FormItemCollection $collection The collection of form items.
     */
    public function __construct(string $name, FormItemCollection $collection)
    {
        // Initialize context
        $context = new ContextData();
        $context->set("name", $name);
        $context->set("formlist", $collection->all());

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
