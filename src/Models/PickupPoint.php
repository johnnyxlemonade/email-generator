<?php

namespace Lemonade\EmailGenerator\Models;

class PickupPoint
{
    /**
     * Constructor for creating an instance of a pickup point.
     *
     * @param string|null $id Identifier of the pickup point (optional).
     * @param string|null $name Name of the pickup point (optional).
     * @param string|null $street Street of the pickup point (optional).
     * @param string|null $city City of the pickup point (optional).
     * @param array<string, string> $openingHours Opening hours in the format ['day' => 'time'] (optional).
     * @param float|null $latitude Latitude of the pickup point (optional).
     * @param float|null $longitude Longitude of the pickup point (optional).
     * @param string|null $googleMapKey Google Maps API key (optional).
     */
    public function __construct(
        protected readonly ?string $id = null,
        protected readonly ?string $name = null,
        protected readonly ?string $street = null,
        protected readonly ?string $city = null,
        protected readonly array $openingHours = [],
        protected readonly ?float $latitude = null,
        protected readonly ?float $longitude = null,
        protected readonly ?string $googleMapKey = null
    ) {}

    /**
     * Returns the ID of the pickup point.
     *
     * @return string|null Identifier of the pickup point, or `null` if not set.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Returns the name of the pickup point.
     *
     * @return string|null Name of the pickup point, or `null` if not set.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Returns the street of the pickup point.
     *
     * @return string|null Street of the pickup point, or `null` if not set.
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * Returns the city of the pickup point.
     *
     * @return string|null City of the pickup point, or `null` if not set.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Returns the opening hours of the pickup point.
     *
     * @return array<string, string> Opening hours in the format ['day' => 'time'].
     */
    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    /**
     * Returns the latitude of the pickup point.
     *
     * @return float|null Latitude of the pickup point, or `null` if not set.
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * Returns the longitude of the pickup point.
     *
     * @return float|null Longitude of the pickup point, or `null` if not set.
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * Returns the Google Maps API key.
     *
     * @return string|null Google Maps API key, or `null` if not set.
     */
    public function getGoogleMapKey(): ?string
    {
        return $this->googleMapKey;
    }

    /**
     * Returns the full address as a formatted string.
     *
     * @return string|null Formatted address in the format "street, city", or `null` if no address information is available.
     */
    public function getFullAddress(): ?string
    {
        // Filter out empty address parts (if street or city is not set)
        $addressParts = array_filter([$this->street, $this->city]);

        // If at least part of the address is available, join them into a string, otherwise return `null`
        return !empty($addressParts) ? implode(', ', $addressParts) : null;
    }

    /**
     * Returns the formatted opening hours as a string.
     *
     * @return string|null Formatted opening hours, or `null` if no opening hours information is available.
     */
    public function getFormattedOpeningHours(): ?string
    {
        // If opening hours are not defined, return `null`
        if (empty($this->openingHours)) {
            return null;
        }

        // Format the opening hours for each day
        $formattedHours = [];
        foreach ($this->openingHours as $day => $hours) {
            $formattedHours[] = "{$day}: {$hours}";
        }

        // Return the individual opening hours joined by <br>
        return implode("<br>", $formattedHours);
    }
}