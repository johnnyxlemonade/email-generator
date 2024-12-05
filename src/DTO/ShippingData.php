<?php

namespace Lemonade\EmailGenerator\DTO;

class ShippingData
{
    /**
     * Constructor for ShippingData.
     *
     * @param string $name The name of the shipping method (default: 'Výchozí doprava').
     * @param float|int|string|null $price The price of the shipping method (default: 0).
     * @param bool $display Whether to display the shipping method (default: true).
     */
    public function __construct(
        public string $name = 'Výchozí doprava',
        public float|int|string|null $price = 0,
        public bool $display = true
    ) {}
}
