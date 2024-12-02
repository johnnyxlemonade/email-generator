<?php

namespace Lemonade\EmailGenerator\Blocks\Order;
use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EccomerceMessage extends AbstractBlock
{

    /**
     * @param string|array|null $message
     */
    public function __construct(string|array|null $message = null)
    {
        // Inicializace kontextu
        $context = new ContextData();

        // Validace typu zprávy a nastavení výchozí hodnoty, pokud není zadána
        if (is_null($message)) {
            $message = "";
        }

        // Pokud je zpráva pole, převedeme všechny prvky na řetězce a spojíme je do jednoho řetězce
        if (is_array($message)) {
            // Filtrujeme pouze hodnoty, které lze převést na řetězec
            $message = array_filter($message, fn($item) => is_scalar($item) || is_null($item) || (is_object($item) && method_exists($item, '__toString')));
            $message = array_map('strval', $message);
            $message = implode("\n", $message);
        }

        // Přidání kolekce produktů do kontextu
        $context->set("message", $message);

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }


    /**
     * Validace požadovaných klíčů v contextu.
     *
     * @return void
     */
    protected function validateContext(): void
    {
        // Seznam požadovaných klíčů pro tento blok
        //$this->validateRequiredKeys(["message"]);
    }

}