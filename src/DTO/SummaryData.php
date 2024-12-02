<?php

namespace Lemonade\EmailGenerator\DTO;

class SummaryData
{
    /**
     * @param string $name Nazev položky (např. 'Mezisoučet', 'Váha')
     * @param string|int|float $value Hodnota položky.
     * @param bool $final Celkovy souhrn
     */
    public function __construct(
        public string $name,
        public string|int|float $value,
        public bool $final = false
    ) {}
}