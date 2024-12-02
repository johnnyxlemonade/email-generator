<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\PickupPointData;
use Lemonade\EmailGenerator\Factories\PickupPointFactory;
use Lemonade\EmailGenerator\Models\PickupPoint;

class PickupPointService
{
    /**
     * Vytvoří instanci `PickupPoint` na základě dat z `PickupPointData`.
     *
     * @param PickupPointData $data
     * @return PickupPoint
     */
    public function createPickupPoint(PickupPointData $data): PickupPoint
    {
        return PickupPointFactory::createFromDTO($data);
    }
}