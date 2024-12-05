<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\PickupPoint;

class ComponentPickupPoint extends AbstractBlock
{
    /**
     * Constructor for `ComponentPickupPoint`.
     *
     * @param PickupPoint $pickupPoint The pickup point information.
     */
    public function __construct(PickupPoint $pickupPoint)
    {
        // Initialize context
        $context = new ContextData();

        // Add pickup point to context
        $context->set("pickupPoint", $pickupPoint);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validate the required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that "pickupPoint" key is present in the context
        $this->context->validate(["pickupPoint"]);
    }
}
