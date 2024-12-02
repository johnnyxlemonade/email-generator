<?php

namespace Lemonade\EmailGenerator\Blocks\Informational;
use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Models\Address;

class StaticNotification extends AbstractBlock
{

    /**
     * @param string|null $heading
     * @param string|null $notification
     */
    public function __construct(string $heading, string $notification)
    {
        // Inicializace kontextu
        $context = new ContextData();

        // Přidání ddresy do kontextu
        $context->set("heading", $heading);
        $context->set("notification", $notification);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function validateContext(): void
    {
        // Předpokládáme, že `heading` a `notification` je už v kontextu nastavena
        $this->context->validate(["heading", "notification"]);
    }

}