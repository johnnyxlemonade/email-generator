<?php

namespace Lemonade\EmailGenerator\Collection;

use Lemonade\EmailGenerator\Models\SummaryItem;

class SummaryCollection extends AbstractCollection
{
    /**
     * Ověří, zda je daný objekt platným typem pro kolekci (v tomto případě instancí třídy SummaryItem).
     *
     * @param mixed $item Objekt, který se ověřuje.
     * @return bool True, pokud je objekt platný (instancí SummaryItem); jinak false.
     */
    protected function validateItem($item): bool
    {
        return $item instanceof SummaryItem;
    }

}
