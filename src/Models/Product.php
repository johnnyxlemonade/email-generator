<?php

namespace Lemonade\EmailGenerator\Models;

class Product
{
    public function __construct(
        protected readonly string|int $productId,
        protected readonly string|int|null $productCode = null,
        protected readonly string|null $productName = null,
        protected readonly float|int|null $productQuantity = null,
        protected readonly float|int|null $productUnitPrice = null,
        protected readonly float|int $productUnitBase = 0,
        protected readonly float|int|null $productTax = 0,
        protected readonly string|null $productUrl = null,
        protected readonly string|null $productImage = null,
        protected readonly array $productData = [],
        protected readonly bool $useData = false
    ) {}

    public function getId(): string|int
    {
        return $this->productId;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function getProductCode(): string|int|null
    {
        return $this->productCode;
    }

    public function getProductQuantity(): ?float
    {
        return $this->productQuantity !== null ? (float) $this->productQuantity : null;
    }

    public function getProductUrl(): ?string
    {
        return $this->productUrl;
    }

    public function getProductImage(): ?string
    {
        return $this->productImage;
    }

    public function getProductUnitPrice(): ?float
    {
        return $this->productUnitPrice !== null ? (float) $this->productUnitPrice : null;
    }

    public function getProductBasePrice(): float
    {
        return (float) $this->productUnitBase;
    }

    public function getProductUnitTax(): float
    {
        return (float) $this->productTax;
    }

    public function getProductSubtotalPrice(): float
    {
        if ($this->productQuantity === null || $this->productUnitPrice === null) {
            return 0.0;
        }
        return (float) $this->productQuantity * (float) $this->productUnitPrice;
    }

    public function useData(): bool
    {
        return $this->useData;
    }

    public function getProductData(): array
    {
        return $this->productData;
    }

    public function getProductDataAttribute(string $index, string $attribute = 'name'): string
    {
        return (string) ($this->productData[$index][$attribute] ?? '');
    }

    public function getProductDataName(string $index): string
    {
        return $this->getProductDataAttribute($index, 'name');
    }

    public function getProductDataText(string $index): string
    {
        return $this->getProductDataAttribute($index, 'text');
    }
}
