<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Services\ContextService;

class EcommerceHeader extends AbstractBlock
{

    /**
     * Constructor for `EcommerceHeader`.
     *
     * @param ContextService $contextService
     * @param string|int $orderId
     * @param string|int $orderCode
     * @param string|int|float $orderTotal
     * @param string $orderCurrency
     * @param string $orderDate
     */
    public function __construct(

        protected readonly ContextService $contextService,
        protected readonly string|int $orderId,
        protected readonly string|int $orderCode,
        protected readonly string|int|float $orderTotal,
        protected readonly string $orderCurrency,
        protected readonly string $orderDate
    ) {

        // Initialize context
        $context = $this->contextService->createContext([
            "orderId" => $orderId,
            "orderCode" => $orderCode,
            "orderTotal" => $orderTotal,
            "orderCurrency" => $orderCurrency,
            "orderDate" => $orderDate,
        ]);

        // Call the parent constructor with the context
        parent::__construct($context);
    }

    /**
     * Validate the required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validation of expected keys in the context
        $this->context->validate(["orderId", "orderCode", "orderTotal", "orderCurrency", "orderDate"]);
    }
}
