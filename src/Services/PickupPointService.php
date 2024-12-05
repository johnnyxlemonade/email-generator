<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\PickupPointData;
use Lemonade\EmailGenerator\Factories\PickupPointFactory;
use Lemonade\EmailGenerator\Models\PickupPoint;

class PickupPointService
{
    /**
     * Creates a new PickupPoint instance from the provided PickupPointData.
     *
     * @param PickupPointData $data Data Transfer Object (DTO) containing pickup point information.
     * @return PickupPoint A new PickupPoint instance based on the provided DTO.
     */
    public function createPickupPoint(PickupPointData $data): PickupPoint
    {
        return PickupPointFactory::createFromDTO($data);
    }
}
