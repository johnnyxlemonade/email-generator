<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\ShippingData;
use Lemonade\EmailGenerator\Factories\ShippingFactory;
use Lemonade\EmailGenerator\Models\Shipping;

class ShippingService
{
    /**
     * Creates a new Shipping instance from the provided ShippingData.
     *
     * @param ShippingData $data Data Transfer Object (DTO) containing shipping information.
     * @return Shipping A new Shipping instance based on the provided DTO.
     */
    public function createShipping(ShippingData $data): Shipping
    {
        return ShippingFactory::createFromDTO($data);
    }
}
