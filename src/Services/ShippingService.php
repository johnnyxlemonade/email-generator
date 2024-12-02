<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\ShippingData;
use Lemonade\EmailGenerator\Factories\ShippingFactory;
use Lemonade\EmailGenerator\Models\Shipping;

class ShippingService
{
    /**
     * Vytvoří instanci `Shipping` na základě dat ze `ShippingData`.
     *
     * @param ShippingData $data
     * @return Shipping
     */
    public function createShipping(ShippingData $data): Shipping
    {
        return ShippingFactory::createFromDTO($data);
    }
}

