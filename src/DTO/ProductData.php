<?php

namespace Lemonade\EmailGenerator\DTO;

class ProductData
{
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