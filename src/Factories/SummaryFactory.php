<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\SummaryItem;
use Lemonade\EmailGenerator\DTO\SummaryData;

class SummaryFactory
{
    /**
     * Creates an instance of `SummaryItem` based on the data from `SummaryData`.
     *
     * @param SummaryData $data The data for creating the summary item.
     * @return SummaryItem A new instance of SummaryItem.
     */
    public static function createFromDTO(SummaryData $data): SummaryItem
    {
        return new SummaryItem(name: $data->name, value: $data->value, final: $data->final);
    }
}
