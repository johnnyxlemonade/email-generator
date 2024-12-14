<?php

namespace Lemonade\EmailGenerator\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Address;
use Lemonade\EmailGenerator\Services\ContextService;

class StaticBlockGreetingAddress extends AbstractBlock
{
    /**
     * Constructor for `StaticBlockGreetingAddress`.
     *
     * @param ContextService $contextData Context Service.
     * @param Address $address The address data for the greeting.
     */
    public function __construct(protected readonly ContextService $contextService, Address $address)
    {
        // Initialize context
        $context = $this->contextService->createContext([
            "address" => $address
        ]);

        // Pass the context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validate the required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Ensure the `address` key is present in the context
        $this->context->validate(["address"]);
    }
}

