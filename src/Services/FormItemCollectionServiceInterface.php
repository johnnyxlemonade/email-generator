<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\FormItemCollection;
use Lemonade\EmailGenerator\DTO\FormItemData;
use Lemonade\EmailGenerator\Models\FormItem;

interface FormItemCollectionServiceInterface
{
    /**
     * Creates a new FormItemCollection.
     *
     * @return FormItemCollection A new instance of FormItemCollection.
     */
    public function createCollection(): FormItemCollection;

    /**
     * Creates a new FormItem from FormItemData and adds it to the collection.
     *
     * @param FormItemCollection $collection The collection to which the form item will be added.
     * @param FormItemData $data Data Transfer Object (DTO) containing form item information.
     * @return void
     */
    public function createItem(FormItemCollection $collection, FormItemData $data): void;

    /**
     * Retrieves all FormItem instances from the given collection.
     *
     * @param FormItemCollection $collection The collection from which to retrieve items.
     * @return FormItem[] An array of FormItem instances.
     */
    public function getAllItems(FormItemCollection $collection): array;
}
