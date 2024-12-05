<?php

namespace Lemonade\EmailGenerator\Models;

class Shipping
{
    /**
     * @param string $name Name of the shipping method.
     * @param float|int|string|null $price Shipping cost (can be of various types but will be converted to float).
     * @param bool $display Flag indicating whether the shipping cost should be displayed (default is `true`).
     */
    public function __construct(
        protected readonly string $name,
        protected readonly float|int|string|null $price,
        protected readonly bool $display = true
    ) {
    }

    /**
     * Returns the name of the shipping method.
     *
     * @return string Name of the shipping.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the shipping cost as a float.
     *
     * @return float|null Returns the shipping cost as a float or `null` if the price is not available.
     */
    public function getPrice(): ?float
    {
        return $this->price !== null ? (float) $this->price : null;
    }

    /**
     * Returns the flag indicating whether the shipping cost should be displayed.
     *
     * @return bool `true` if the price should be displayed, otherwise `false`.
     */
    public function shouldDisplay(): bool
    {
        return $this->display;
    }
}
