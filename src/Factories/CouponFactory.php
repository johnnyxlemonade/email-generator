<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\DTO\CouponData;
use Lemonade\EmailGenerator\Models\Coupon;

/**
 * Class CouponFactory
 * Provides factory methods for creating instances of Coupon.
 */
class CouponFactory
{

    /**
     * Creates an instance of `Coupon` based on the data from `CouponData`.
     *
     * @param CouponData $data The data for creating the coupon.
     * @return Coupon A new instance of Coupon.
     */
    public static function createFromDTO(CouponData $data): Coupon
    {
        return new Coupon(
            $data->name,
            $data->code,
            $data->price,
            $data->image
        );
    }

}