<?php

namespace Lemonade\EmailGenerator\Models;

class FormItem
{
    /**
     * @param string $name Name of the form item
     * @param string|null $value Value of the form item
     */
    public function __construct(
        protected readonly string $name,
        protected readonly ?string $value
    ) {}

    /**
     * Returns the name of the form item.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the value of the form item.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}