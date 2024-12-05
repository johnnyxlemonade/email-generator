<?php

namespace Lemonade\EmailGenerator\Models;

class SummaryItem
{
    /**
     * @param string $name Name of the item (e.g., 'Subtotal', 'Weight').
     * @param string|int|float $value Value of the item.
     * @param bool $final Indicates if this is the final summary.
     */
    public function __construct(
        protected readonly string $name,
        protected readonly string|int|float $value,
        protected bool $final = false
    ) {}

    /**
     * Returns the name of the item.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the value of the item.
     *
     * @return string|int|float
     */
    public function getValue(): string|int|float
    {
        return $this->value;
    }

    /**
     * Checks if the value is an int or float, indicating it can be rounded.
     *
     * @return bool
     */
    public function isNumber(): bool
    {
        return is_int($this->value) || is_float($this->value);
    }

    /**
     * Returns whether the summary is final.
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->final;
    }
}
