<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\DTO\ProductData;
use Lemonade\EmailGenerator\Factories\ProductFactory;
use Lemonade\EmailGenerator\Models\Product;

class ProductCollectionService
{
    private ProductFactory $productFactory;

    /**
     * Konstruktor `ProductCollectionService`
     * Zajišťuje, že `ProductFactory` je předán a k dispozici při vytváření služby.
     *
     * @param ProductFactory $productFactory Továrna pro vytváření produktů.
     */
    public function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * Vytváří novou instanci `ProductCollection`.
     *
     * @return ProductCollection
     */
    public function createProductCollection(): ProductCollection
    {
        return new ProductCollection();
    }

    /**
     * Přidává produkt do kolekce na základě dat `ProductData`.
     *
     * @param ProductCollection $collection Kolekce produktů, kam se má přidat produkt.
     * @param ProductData $data Data pro vytvoření produktu.
     * @return void
     */
    public function addProductToCollection(ProductCollection $collection, ProductData $data): void
    {
        // Použití továrny k vytvoření produktu z dat DTO
        $product = $this->productFactory->createFromDTO($data);
        // Přidání produktu do kolekce
        $collection->add($product);
    }

    /**
     * Vrací všechny produkty z kolekce.
     *
     * @param ProductCollection $collection
     * @return Product[]
     */
    public function getAllProducts(ProductCollection $collection): array
    {
        return $collection->all();
    }
}

