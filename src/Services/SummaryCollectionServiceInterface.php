<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\DTO\SummaryData;

interface SummaryCollectionServiceInterface
{
    /**
     * Creates a new SummaryCollection.
     *
     * @return SummaryCollection A new instance of SummaryCollection.
     */
    public function createCollection(): SummaryCollection;

    /**
     * Creates a new item from SummaryData and adds it to the collection.
     *
     * @param SummaryCollection $collection The collection to which the summary item will be added.
     * @param SummaryData $data Data Transfer Object (DTO) containing summary information.
     * @return void
     */
    public function createItem(SummaryCollection $collection, SummaryData $data): void;
}
