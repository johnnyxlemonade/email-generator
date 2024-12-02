<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Collection\AbstractCollection;

class EccomerceProductList extends AbstractBlock
{

    /**
     * @param AbstractCollection $collection
     * @param string $currency
     */
    public function __construct(AbstractCollection $collection, string $currency)
    {

        // Inicializace kontextu
        $context = new ContextData();
        $context->set("products", $collection->all());
        $context->set("currency", $currency);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function validateContext(): void
    {
        $this->context->validate(["products", "currency"]);
    }
}
