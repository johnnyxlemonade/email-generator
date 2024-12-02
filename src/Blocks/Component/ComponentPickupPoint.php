<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\PickupPoint;

class ComponentPickupPoint extends AbstractBlock
{

    /**
     * @param PickupPoint $pickupPoint
     */
    public function __construct(PickupPoint $pickupPoint)
    {
        // Inicializace kontextu
        $context = new ContextData();

        // Přidání ddresy do kontextu
        $context->set("pickupPoint", $pickupPoint);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function validateContext(): void
    {
        // Validace očekávaných klíčů
        $this->context->validate(["pickupPoint"]);
    }
}