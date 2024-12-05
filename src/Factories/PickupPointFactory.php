<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\PickupPoint;
use Lemonade\EmailGenerator\DTO\PickupPointData;

class PickupPointFactory
{
    /**
     * Creates an instance of PickupPoint based on DTO data.
     *
     * @param PickupPointData $data The data for creating the PickupPoint instance.
     * @return PickupPoint A new instance of PickupPoint.
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
