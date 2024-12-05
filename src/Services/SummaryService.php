<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Factories\SummaryFactory;

class SummaryService
{
    /**
     * Retrieves a new instance of SummaryCollection.
     *
     * @return SummaryCollection A new instance of SummaryCollection.
     */
    public function getSummaryCollection(): SummaryCollection
    {
        return new SummaryCollection();
    }

    /**
     * Adds a SummaryItem to the given SummaryCollection.
     *
     * @param SummaryCollection $collection The collection to which the summary item will be added.
     * @param SummaryData $data Data Transfer Object (DTO) containing summary information.
     * @return void
     */
    public function addSummaryItemToCollection(SummaryCollection $collection, SummaryData $data): void
    {
        // Use the factory to create a summary item from the DTO data
        $summaryItem = SummaryFactory::createFromDTO($data);

        // Add the summary item to the collection
        $collection->add($summaryItem);
    }
}