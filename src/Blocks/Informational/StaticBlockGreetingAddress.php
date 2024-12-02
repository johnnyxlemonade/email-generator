<?php

namespace Lemonade\EmailGenerator\Blocks\Informational;
use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Address;

class StaticBlockGreetingAddress extends AbstractBlock
{

    /**
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        // Inicializace kontextu
        $context = new ContextData();

        // Přidání ddresy do kontextu
        $context->set("address", $address);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * @return void
     */
    protected function validateContext(): void
    {
        // Předpokládáme, že `address` je už v kontextu nastavena
        $this->context->validate(["address"]);
    }

}
