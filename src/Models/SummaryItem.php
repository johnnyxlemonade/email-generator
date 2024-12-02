<?php

namespace Lemonade\EmailGenerator\Models;

class SummaryItem
{
    /**
     * @param string $name Nazev položky (např. 'Mezisoučet', 'Váha').
     * @param string|int|float $value Hodnota položky.
     * @param bool $final Celkovy souhrn
     */
    public function __construct(
        protected readonly string $name,
        protected readonly string|int|float $value,
        protected bool $final = false
    ) {}

    /**
     * Vrací nazev položky.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Vrací hodnotu položky.
     *
     * @return string|int|float
     */
    public function getValue(): string|int|float
    {
        return $this->value;
    }

    /**
     * Testujeme, zda je hodnota int nebo float, aby sla zaokrhlouhlovat
     *
     * @return bool
     */
    public function isNumber(): bool
    {
        if(is_int($this->value) || is_float($this->value)) {

            return true;
        }

        return false;
    }

    /**
     * Vraci, zda je souhrn "final"
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->final;
    }
}
