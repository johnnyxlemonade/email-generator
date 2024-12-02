<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\PickupPoint;
use Lemonade\EmailGenerator\DTO\PickupPointData;

class PickupPointFactory
{
    /**
     * Vytvoří instanci PickupPoint na základě DTO dat.
     *
     * @param PickupPointData $data
     * @return PickupPoint
     */
    public static function createFromDTO(PickupPointData $data): PickupPoint
    {
        return new PickupPoint(
            id: $data->id,
            name: $data->name,
            street: $data->street,
            city: $data->city,
            openingHours: $data->openingHours,
            latitude: $data->latitude,
            longitude: $data->longitude,
            googleMapKey: $data->googleMapKey
        );
    }
}