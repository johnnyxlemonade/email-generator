<?php

namespace Lemonade\EmailGenerator\Collection;

use Lemonade\EmailGenerator\Models\Coupon;

/**
 * Class CouponCollection
 * Represents a collection of Coupon objects.
 */
class CouponCollection extends AbstractCollection
{
    /**
     * Validates whether the given object is a valid type for the collection (in this case, an instance of Coupon).
     *
     * @param mixed $item The object being validated.
     * @return bool True if the object is valid (an instance of Coupon); otherwise false.
     */
    protected function validateItem($item): bool
    {
        return $item instanceof Coupon;
    }
}