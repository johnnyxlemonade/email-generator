<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Product;
use Lemonade\EmailGenerator\DTO\ProductData;

class ProductFactory
{
    /**
     * Vytvoří instanci `Product` na základě dat v `ProductData`.
     *
     * @param ProductData $data Data potřebná k vytvoření `Product`.
     * @return Product
     */
    public static function createFromDTO(ProductData $data): Product
    {
        return new Product(
            productId: $data->productId,
            productCode: $data->productCode,
            productName: $data->productName,
            productQuantity: $data->productQuantity,
            productUnitPrice: $data->productUnitPrice,
            productUnitBase: $data->productUnitBase,
            productTax: $data->productTax,
            productUrl: $data->productUrl,
            productImage: $data->productImage,
            productData: $data->productData,
            useData: $data->useData
        );
    }
}