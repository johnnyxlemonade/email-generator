<?php

namespace Lemonade\EmailGenerator\DTO;

class PaymentData
{
    public function __construct(
        public string $name = 'Výchozí platba',
        public float|int|string|null $price = 0,
        public bool $display = true
    ) {}
}