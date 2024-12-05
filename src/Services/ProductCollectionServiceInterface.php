<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\DTO\ProductData;
use Lemonade\EmailGenerator\Models\Product;

interface ProductCollectionServiceInterface
{
    /**
     * Creates a new ProductCollection.
     *
     * @return ProductCollection A new instance of ProductCollection.
     */
    public function createCollection(): ProductCollection;

    /**
     * Creates a new Product from ProductData and adds it to the collection.
     *
     * @param ProductCollection $collection The collection to which the product will be added.
     * @param ProductData $data Data Transfer Object (DTO) containing product information.
     * @return void
     */
    public function createItem(ProductCollection $collection, ProductData $data): void;

    /**
     * Retrieves all Product instances from the given collection.
     *
     * @param ProductCollection $collection The collection from which to retrieve items.
     * @return Product[] An array of Product instances.
     */
    public function getAllItems(ProductCollection $collection): array;
}
