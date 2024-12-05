<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\FormItemCollection;
use Lemonade\EmailGenerator\DTO\FormItemData;
use Lemonade\EmailGenerator\Factories\FormItemFactory;
use Lemonade\EmailGenerator\Models\FormItem;

class FormItemCollectionService extends AbstractCollectionService implements FormItemCollectionServiceInterface
{
    /**
     * @var FormItemFactory Factory for creating FormItem instances.
     */
    private FormItemFactory $formItemFactory;

    /**
     * Constructor for FormItemCollectionService.
     * Initializes the service with a FormItemFactory.
     *
     * @param FormItemFactory $formItemFactory Factory for creating FormItem instances.
     */
    public function __construct(FormItemFactory $formItemFactory)
    {
        $this->formItemFactory = $formItemFactory;
    }

    /**
     * Creates a new FormItemCollection.
     *
     * @return FormItemCollection A new instance of FormItemCollection.
     */
    public function createCollection(): FormItemCollection
    {
        return new FormItemCollection();
    }

    /**
     * Creates a new FormItem from FormItemData and adds it to the collection.
     *
     * @param FormItemCollection $collection The collection to which the form item will be added.
     * @param FormItemData $data Data Transfer Object (DTO) containing form item information.
     * @return void
     */
    public function createItem(FormItemCollection $collection, FormItemData $data): void
    {
        // Use the factory to create a form item from the DTO data
        $item = $this->formItemFactory->createFromDTO($data);

        // Add the form item to the collection
        $collection->add($item);
    }
}
