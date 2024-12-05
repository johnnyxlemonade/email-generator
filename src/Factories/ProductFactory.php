<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Product;
use Lemonade\EmailGenerator\DTO\ProductData;

class ProductFactory
{
    /**
     * Creates an instance of `Product` based on the data in `ProductData`.
     *
     * @param ProductData $data The data needed to create the `Product` instance.
     * @return Product A new instance of Product.
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
