<?php

namespace Lemonade\EmailGenerator\DTO;

class ProductData
{
    /**
     * Constructor for ProductData.
     *
     * @param string|int $productId The ID of the product.
     * @param string|int|null $productCode The code of the product (optional).
     * @param string|null $productName The name of the product (optional).
     * @param float|int|null $productQuantity The quantity of the product (optional).
     * @param float|int|null $productUnitPrice The unit price of the product (optional).
     * @param float|int $productUnitBase The base unit of the product (default: 0).
     * @param float|int|null $productTax The tax of the product (default: 0).
     * @param string|null $productUrl The URL of the product (optional).
     * @param string|null $productImage The image URL of the product (optional).
     * @param array $productData Additional product data (default: empty array).
     * @param bool $useData Whether to use additional product data (default: false).
     */
    public function __construct(
        public string|int $productId,
        public string|int|null $productCode = null,
        public ?string $productName = null,
        public float|int|null $productQuantity = null,
        public float|int|null $productUnitPrice = null,
        public float|int $productUnitBase = 0,
        public float|int|null $productTax = 0,
        public ?string $productUrl = null,
        public ?string $productImage = null,
        public array $productData = [],
        public bool $useData = false
    ) {}
}
