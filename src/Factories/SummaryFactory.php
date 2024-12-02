<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\SummaryItem;
use Lemonade\EmailGenerator\DTO\SummaryData;

class SummaryFactory
{
    /**
     * Vytvoří instanci `SummaryItem` na základě dat z `SummaryData`.
     *
     * @param SummaryData $data Data pro vytvoření položky souhrnu.
     * @return SummaryItem
     */
    public static function createFromDTO(SummaryData $data): SummaryItem
    {
        return new SummaryItem(name: $data->name, value: $data->value, final: $data->final);
    }
}