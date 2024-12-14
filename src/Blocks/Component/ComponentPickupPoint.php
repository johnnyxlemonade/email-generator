<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\PickupPoint;
use Lemonade\EmailGenerator\Services\ContextService;

class ComponentPickupPoint extends AbstractBlock
{
    /**
     * Constructor for `ComponentPickupPoint`.
     *
     * @param ContextService $contextService Context Service.
     * @param PickupPoint $pickupPoint The pickup point information.
     */
    public function __construct(protected readonly ContextService $contextService, PickupPoint $pickupPoint)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "pickupPoint" => $pickupPoint
        ]);

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
