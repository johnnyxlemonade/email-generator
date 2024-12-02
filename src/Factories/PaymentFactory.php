<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\DTO\PaymentData;

class PaymentFactory
{
    /**
     * Vytvoří instanci Payment na základě DTO dat.
     *
     * @param PaymentData $data
     * @return Payment
     */
    public static function createFromDTO(PaymentData $data): Payment
    {
        return new Payment($data->name, $data->price, $data->display);
    }
}