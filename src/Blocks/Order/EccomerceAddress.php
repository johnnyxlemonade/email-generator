<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Address;

class EccomerceAddress extends AbstractBlock
{

    /**
     * @param Address $billingAddress
     * @param Address $deliveryAddress
     */
    public function __construct(Address $billingAddress, Address $deliveryAddress)
    {
        // Inicializace kontextu
        $context = new ContextData();

        // Přidání ddresy do kontextu
        $context->set("billingAddress", $billingAddress);
        $context->set("deliveryAddress", $deliveryAddress);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * @return void
     */
    protected function validateContext(): void
    {
        // Předpokládáme, že `address` jsou už v kontextu nastaveny
        $this->context->validate(["billingAddress", "deliveryAddress"]);
    }

}