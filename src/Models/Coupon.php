<?php

namespace Lemonade\EmailGenerator\Models;

/**
 * Class Coupon
 * Represents a coupon with details such as name, code, validity, and an optional image.
 */
class Coupon
{

    /**
     * @param string $name The name of the coupon.
     * @param string $code The unique code for the coupon.
     * @param string|int|float|null $price The price the coupon in string format (optional).
     * @param string|null $image The URL or path to an image associated with the coupon (optional).
     */
    public function __construct(
        protected readonly string $name,
        protected readonly string $code,
        protected readonly string|int|float|null $price = null,
        protected readonly ?string $image = null
    ) {}

    /**
     * Get the name of the coupon.
     *
     * @return string The name of the coupon.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the code of the coupon.
     *
     * @return string The unique code for the coupon.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the price of the coupon.
     *
     * @return string|int|float|null The price of the coupon, or null if no price is provided.
     */
    public function getPrice(): string|int|float|null
    {
        return $this->price;
    }

    /**
     * Returns the product unit price as a float.
     *
     * @return float|null
     */
    public function getFormattedPrice(): ?float
    {
        return $this->price !== null ? (float) $this->price : null;
    }

    /**
     * Get the image URL or path associated with the coupon.
     *
     * @return string|null The URL or path to the image, or null if no image is provided.
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
}