<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\PaymentData;
use Lemonade\EmailGenerator\Factories\PaymentFactory;
use Lemonade\EmailGenerator\Models\Payment;

class PaymentService
{
    /**
     * Creates a new Payment instance from the provided PaymentData.
     *
     * @param PaymentData $data Data Transfer Object (DTO) containing payment information.
     * @return Payment A new Payment instance based on the provided DTO.
     */
    public function createPayment(PaymentData $data): Payment
    {
        return PaymentFactory::createFromDTO($data);
    }
}
