<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Factories\SummaryFactory;
use Lemonade\EmailGenerator\Models\SummaryItem;

class SummaryCollectionService extends AbstractCollectionService implements SummaryCollectionServiceInterface
{
    /**
     * Constructor for SummaryCollectionService.
     * Initializes the service with a SummaryFactory.
     *
     * @param SummaryFactory $summaryFactory Factory for creating SummaryItem instances.
     */
    public function __construct(protected readonly SummaryFactory $summaryFactory)
    {
    }

    /**
     * Creates a new SummaryCollection.
     *
     * @return SummaryCollection A new instance of SummaryCollection.
     */
    public function createCollection(): SummaryCollection
    {
        return new SummaryCollection();
    }

    /**
     * Creates a new SummaryItem from SummaryData and adds it to the collection.
     *
     * @param SummaryCollection $collection The collection to which the summary item will be added.
     * @param SummaryData $data Data Transfer Object (DTO) containing summary information.
     * @return void
     */
    public function createItem(SummaryCollection $collection, SummaryData $data): void
    {
        // Use the factory to create a summary item from the DTO data
        $product = $this->summaryFactory->createFromDTO($data);

        // Add the summary item to the collection
        $collection->add($product);
    }
}
