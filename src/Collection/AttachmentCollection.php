<?php

namespace Lemonade\EmailGenerator\Collection;

use Lemonade\EmailGenerator\Models\Attachment;

/**
 * Class AttachmentCollection
 * Represents a collection of Attachment objects.
 */
class AttachmentCollection extends AbstractCollection
{
    /**
     * Validates whether the given object is a valid type for the collection (in this case, an instance of Attachment).
     *
     * @param mixed $item The object being validated.
     * @return bool True if the object is valid (an instance of Attachment); otherwise false.
     */
    protected function validateItem($item): bool
    {
        return $item instanceof Attachment;
    }
}
