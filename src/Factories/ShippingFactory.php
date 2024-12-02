<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Shipping;
use Lemonade\EmailGenerator\DTO\ShippingData;

class ShippingFactory
{
    /**
     * Vytvoří instanci Shipping na základě DTO dat.
     *
     * @param ShippingData $data
     * @return Shipping
     */
    public static function createFromDTO(ShippingData $data): Shipping
    {
        return new Shipping($data->name, $data->price, $data->display);
    }
}