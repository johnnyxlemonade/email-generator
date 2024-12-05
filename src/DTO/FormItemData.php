<?php

namespace Lemonade\EmailGenerator\DTO;

class FormItemData
{
    /**
     * Constructor for FormItemData.
     *
     * @param string $name The name of the form item.
     * @param string|null $value The value of the form item (optional).
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $value
    ) {}
}
