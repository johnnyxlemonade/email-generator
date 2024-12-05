<?php

namespace Lemonade\EmailGenerator\Models;

class Product
{
    /**
     * Constructor for creating a product instance.
     *
     * @param string|int $productId Product ID.
     * @param string|int|null $productCode Product code (optional).
     * @param string|null $productName Product name (optional).
     * @param float|int|null $productQuantity Product quantity (optional).
     * @param float|int|null $productUnitPrice Product unit price (optional).
     * @param float|int $productUnitBase Base unit price (default is 0).
     * @param float|int|null $productTax Product tax (optional, default is 0).
     * @param string|null $productUrl Product URL (optional).
     * @param string|null $productImage Product image URL (optional).
     * @param array $productData Additional product data (optional).
     * @param bool $useData Flag to indicate if additional data should be used (default is false).
     */
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

    /**
     * Returns the product ID.
     *
     * @return string|int
     */
    public function getId(): string|int
    {
        return $this->productId;
    }

    /**
     * Returns the product name.
     *
     * @return string|null
     */
    public function getProductName(): ?string
    {
        return $this->productName;
    }

    /**
     * Returns the product code.
     *
     * @return string|int|null
     */
    public function getProductCode(): string|int|null
    {
        return $this->productCode;
    }

    /**
     * Returns the product quantity as a float.
     *
     * @return float|null
     */
    public function getProductQuantity(): ?float
    {
        return $this->productQuantity !== null ? (float) $this->productQuantity : null;
    }

    /**
     * Returns the product URL.
     *
     * @return string|null
     */
    public function getProductUrl(): ?string
    {
        return $this->productUrl;
    }

    /**
     * Returns the product image URL.
     *
     * @return string|null
     */
    public function getProductImage(): ?string
    {
        return $this->productImage;
    }

    /**
     * Returns the product unit price as a float.
     *
     * @return float|null
     */
    public function getProductUnitPrice(): ?float
    {
        return $this->productUnitPrice !== null ? (float) $this->productUnitPrice : null;
    }

    /**
     * Returns the base unit price of the product.
     *
     * @return float
     */
    public function getProductBasePrice(): float
    {
        return (float) $this->productUnitBase;
    }

    /**
     * Returns the unit tax of the product.
     *
     * @return float
     */
    public function getProductUnitTax(): float
    {
        return (float) $this->productTax;
    }

    /**
     * Returns the subtotal price of the product.
     *
     * @return float
     */
    public function getProductSubtotalPrice(): float
    {
        if ($this->productQuantity === null || $this->productUnitPrice === null) {
            return 0.0;
        }
        return (float) $this->productQuantity * (float) $this->productUnitPrice;
    }

    /**
     * Returns whether additional data should be used.
     *
     * @return bool
     */
    public function useData(): bool
    {
        return $this->useData;
    }

    /**
     * Returns the additional product data.
     *
     * @return array
     */
    public function getProductData(): array
    {
        return $this->productData;
    }

    /**
     * Returns a specific attribute from the product data.
     *
     * @param string $index Index of the data.
     * @param string $attribute Attribute name (default is 'name').
     * @return string
     */
    public function getProductDataAttribute(string $index, string $attribute = 'name'): string
    {
        return (string) ($this->productData[$index][$attribute] ?? '');
    }

    /**
     * Returns the 'name' attribute from the product data.
     *
     * @param string $index Index of the data.
     * @return string
     */
    public function getProductDataName(string $index): string
    {
        return $this->getProductDataAttribute($index, 'name');
    }

    /**
     * Returns the 'text' attribute from the product data.
     *
     * @param string $index Index of the data.
     * @return string
     */
    public function getProductDataText(string $index): string
    {
        return $this->getProductDataAttribute($index, 'text');
    }
}
