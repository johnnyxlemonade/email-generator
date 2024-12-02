<?php

namespace Lemonade\EmailGenerator\DTO;

class ShippingData
{
    public function __construct(
        public string $name = 'Výchozí doprava',
        public float|int|string|null $price = 0,
        public bool $display = true
    ) {}
}
