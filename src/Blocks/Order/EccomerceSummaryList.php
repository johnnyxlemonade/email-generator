<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\Context\ContextData;

class EccomerceSummaryList extends AbstractBlock
{
    /**
     * @param SummaryCollection $collection Kolekce souhrnů objednávky.
     * @param string $currency
     */
    public function __construct(SummaryCollection $collection, string $currency)
    {

        // Inicializace kontextu
        $context = new ContextData();
        $context->set("summary", $collection->all());
        $context->set("currency", $currency);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * Validace kontextu pro souhrn objednávky.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validace, že některé základní klíče jsou nastaveny
        $this->context->validate(["summary", "currency"]);
    }
}