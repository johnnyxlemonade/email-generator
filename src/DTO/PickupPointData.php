<?php

namespace Lemonade\EmailGenerator\DTO;

class PickupPointData
{
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