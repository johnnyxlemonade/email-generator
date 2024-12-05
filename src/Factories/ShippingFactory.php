<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Shipping;
use Lemonade\EmailGenerator\DTO\ShippingData;

class ShippingFactory
{
    /**
     * Creates an instance of Shipping based on DTO data.
     *
     * @param ShippingData $data The data for creating the Shipping instance.
     * @return Shipping A new instance of Shipping.
     */
    public static function createFromDTO(ShippingData $data): Shipping
    {
        return new Shipping($data->name, $data->price, $data->display);
    }
}
