<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Factories\SummaryFactory;

class SummaryService
{
    public function getSummaryCollection(): SummaryCollection
    {
        return new SummaryCollection();
    }

    public function addSummaryItemToCollection(SummaryCollection $collection, SummaryData $data): void
    {
        $summaryItem = SummaryFactory::createFromDTO($data);
        $collection->add($summaryItem);
    }
}