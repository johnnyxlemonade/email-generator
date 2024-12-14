<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Address;
use Lemonade\EmailGenerator\Services\ContextService;

class EcommerceAddress extends AbstractBlock
{
    /**
     * Constructor for `EcommerceAddress`.
     *
     * @param ContextService $contextService Context Service.
     * @param Address $billingAddress Billing address.
     * @param Address|null $deliveryAddress Delivery address (optional). If not provided, it defaults to billing address.
     */
    public function __construct(protected readonly ContextService $contextService, Address $billingAddress,  ?Address $deliveryAddress = null)
    {

        if ($deliveryAddress === null) {
            $deliveryAddress = $billingAddress;
        }

        // Add addresses to context
        $context = $this->contextService->createContext([
            "billingAddress" => $billingAddress,
            "deliveryAddress" => $deliveryAddress,
        ]);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Assume `address` is already set in the context
        $this->context->validate(["billingAddress", "deliveryAddress"]);
    }
}
