<?php

namespace Lemonade\EmailGenerator\DTO;

/**
 * Class CouponData
 * Represents the data transfer object for a Coupon.
 */
class CouponData
{

    /**
     * Constructor for CouponData.
     *
     * @param string|null $name The name of the coupon.
     * @param string|null $code The unique code for the coupon.
     * @param string|int|float|null $price The price the coupon in string format (optional).
     * @param string|null $image The URL or path to an image associated with the coupon (optional).
     */
    public function __construct(
        public string $name,
        public string $code,
        public string|int|float|null $price = null,
        public ?string $image = null
    ) {}

}