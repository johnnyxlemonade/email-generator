<?php

namespace Lemonade\EmailGenerator\DTO;

class PickupPointData
{
    /**
     * Constructor for PickupPointData.
     *
     * @param string|null $id The identifier of the pickup point (optional).
     * @param string|null $name The name of the pickup point (optional).
     * @param string|null $street The street address of the pickup point (optional).
     * @param string|null $city The city where the pickup point is located (optional).
     * @param array $openingHours The opening hours of the pickup point (default: empty array).
     * @param float|null $latitude The latitude of the pickup point (optional).
     * @param float|null $longitude The longitude of the pickup point (optional).
     * @param string|null $googleMapKey The Google Maps API key for the pickup point (optional).
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $street = null,
        public ?string $city = null,
        public array $openingHours = [],
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $googleMapKey = null
    ) {}
}