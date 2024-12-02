<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\PaymentData;
use Lemonade\EmailGenerator\Factories\PaymentFactory;
use Lemonade\EmailGenerator\Models\Payment;

class PaymentService
{
    /**
     * Vytvoří instanci `Payment` na základě dat ze `PaymentData`.
     *
     * @param PaymentData $data
     * @return Payment
     */
    public function createPayment(PaymentData $data): Payment
    {
        return PaymentFactory::createFromDTO($data);
    }
}