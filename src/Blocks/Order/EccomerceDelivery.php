<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\Models\Shipping;

class EccomerceDelivery extends AbstractBlock
{
    /**
     * @param Shipping $shipping Informace o způsobu dopravy.
     * @param Payment $payment Informace o způsobu platby.
     * @param string $currency
     */
    public function __construct(Shipping $shipping, Payment $payment, string $currency)
    {
        // Inicializace kontextu
        $context = new ContextData();

        // Přidání dopravy a platby do kontextu
        $context->set("shipping", $shipping);
        $context->set("payment", $payment);
        $context->set("currency", $currency);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * Validace požadovaných klíčů v kontextu.
     *
     * @return void
     */
    public function validateContext(): void
    {

        $this->context->validate(["shipping", "payment", "currency"]);
    }
}