<?php

namespace Lemonade\EmailGenerator\Blocks\Order;
use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EccomerceHeader extends AbstractBlock
{

    /**
     * @param ContextData $context
     */
    public function __construct(ContextData $context)
    {

        parent::__construct($context);
    }

    /**
     * @return void
     */
    protected function validateContext(): void
    {
        // Validace očekávaných klíčů
        $this->context->validate(["orderId", "orderCode", "orderTotal", "orderCurrency", "orderDate"]);
    }

}