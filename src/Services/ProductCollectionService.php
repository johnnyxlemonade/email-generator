<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\DTO\ProductData;
use Lemonade\EmailGenerator\Factories\ProductFactory;
use Lemonade\EmailGenerator\Models\Product;

class ProductCollectionService extends AbstractCollectionService implements ProductCollectionServiceInterface
{
    /**
     * Constructor for ProductCollectionService.
     * Initializes the service with a ProductFactory for creating Product instances.
     *
     * @param ProductFactory $productFactory Factory for creating Product instances.
     */
    public function __construct(private readonly ProductFactory $productFactory)
    {
    }

    /**
     * Creates a new ProductCollection.
     *
     * @return ProductCollection A new instance of ProductCollection.
     */
    public function createCollection(): ProductCollection
    {
        return new ProductCollection();
    }

    /**
     * Creates a new Product item from ProductData and adds it to the collection.
     *
     * @param ProductCollection $collection The collection to which the product will be added.
     * @param ProductData $data Data Transfer Object (DTO) containing product information.
     */
    public function createItem(ProductCollection $collection, ProductData $data): void
    {
        // Use the factory to create a product from the DTO data
        $product = $this->productFactory->createFromDTO($data);

        // Add the product to the collection
        $collection->add($product);
    }
}
