<?php

namespace Lemonade\EmailGenerator\Collection;

use Lemonade\EmailGenerator\Models\FormItem;

class FormItemCollection extends AbstractCollection
{
    /**
     * Validates whether the given object is a valid type for the collection (in this case, an instance of FormItem).
     *
     * @param mixed $item The object being validated.
     * @return bool True if the object is valid (an instance of FormItem); otherwise false.
     */
    protected function validateItem($item): bool
    {
        return $item instanceof FormItem;
    }
}
