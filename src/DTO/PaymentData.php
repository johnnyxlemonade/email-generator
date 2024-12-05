<?php

namespace Lemonade\EmailGenerator\DTO;

class PaymentData
{
    /**
     * Constructor for PaymentData.
     *
     * @param string $name The name of the payment (default: 'Výchozí platba').
     * @param float|int|string|null $price The price of the payment (default: 0).
     * @param bool $display Whether to display the payment (default: true).
     */
    public function __construct(
        public string $name = 'Výchozí platba',
        public float|int|string|null $price = 0,
        public bool $display = true
    ) {}
}
