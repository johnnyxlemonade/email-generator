<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\DTO\PaymentData;

class PaymentFactory
{
    /**
     * Creates an instance of Payment based on DTO data.
     *
     * @param PaymentData $data The data for creating the Payment instance.
     * @return Payment A new instance of Payment.
     */
    public static function createFromDTO(PaymentData $data): Payment
    {
        return new Payment($data->name, $data->price, $data->display);
    }
}
