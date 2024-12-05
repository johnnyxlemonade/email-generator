<?php

namespace Lemonade\EmailGenerator\DTO;

class SummaryData
{
    /**
     * Constructor for SummaryData.
     *
     * @param string $name The name of the item (e.g., 'Subtotal', 'Weight').
     * @param string|int|float $value The value of the item.
     * @param bool $final Whether this is the final summary (default: false).
     */
    public function __construct(
        public string $name,
        public string|int|float $value,
        public bool $final = false
    ) {}
}
