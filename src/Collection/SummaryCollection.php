<?php

namespace Lemonade\EmailGenerator\Collection;

use Lemonade\EmailGenerator\Models\SummaryItem;

/**
 * Class SummaryCollection
 * Represents a collection of SummaryItem objects.
 */
class SummaryCollection extends AbstractCollection
{
    /**
     * Validates whether the given object is a valid type for the collection (in this case, an instance of SummaryItem).
     *
     * @param mixed $item The object being validated.
     * @return bool True if the object is valid (an instance of SummaryItem); otherwise false.
     */
    protected function validateItem($item): bool
    {
        return $item instanceof SummaryItem;
    }
}
