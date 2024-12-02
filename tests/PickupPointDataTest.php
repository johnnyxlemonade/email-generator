<?php

namespace Lemonade\EmailGenerator\Tests\DTO;

use Lemonade\EmailGenerator\DTO\PickupPointData;
use PHPUnit\Framework\TestCase;

class PickupPointDataTest extends TestCase
{
    /**
     * Testuje základní inicializaci objektu PickupPointData
     *
     * Ověřuje, že všechny vlastnosti jsou správně inicializovány,
     * pokud jsou předány hodnoty.
     */
    public function testInitializationWithValues(): void
    {
        $id = "123";
        $name = "Test Pickup Point";
        $street = "Test Street 123";
        $city = "Test City";
        $openingHours = [
            "Pondělí" => "9:00 - 17:00",
            "Úterý" => "9:00 - 17:00"
        ];
        $latitude = 50.0755;
        $longitude = 14.4378;
        $googleMapKey = "testGoogleMapKey";

        $pickupPointData = new PickupPointData(
            id: $id,
            name: $name,
            street: $street,
            city: $city,
            openingHours: $openingHours,
            latitude: $latitude,
            longitude: $longitude,
            googleMapKey: $googleMapKey
        );

        $this->assertSame($id, $pickupPointData->id);
        $this->assertSame($name, $pickupPointData->name);
        $this->assertSame($street, $pickupPointData->street);
        $this->assertSame($city, $pickupPointData->city);
        $this->assertSame($openingHours, $pickupPointData->openingHours);
        $this->assertSame($latitude, $pickupPointData->latitude);
        $this->assertSame($longitude, $pickupPointData->longitude);
        $this->assertSame($googleMapKey, $pickupPointData->googleMapKey);
    }

    /**
     * Testuje inicializaci objektu PickupPointData bez předaných hodnot
     *
     * Ověřuje, že všechny vlastnosti jsou inicializovány s výchozími hodnotami.
     */
    public function testInitializationWithDefaultValues(): void
    {
        $pickupPointData = new PickupPointData();

        $this->assertNull($pickupPointData->id);
        $this->assertNull($pickupPointData->name);
        $this->assertNull($pickupPointData->street);
        $this->assertNull($pickupPointData->city);
        $this->assertSame([], $pickupPointData->openingHours);
        $this->assertNull($pickupPointData->latitude);
        $this->assertNull($pickupPointData->longitude);
        $this->assertNull($pickupPointData->googleMapKey);
    }

    /**
     * Testuje změnu hodnot ve vlastnostech objektu PickupPointData
     *
     * Ověřuje, že vlastnosti mohou být úspěšně změněny po vytvoření objektu.
     */
    public function testPropertyModification(): void
    {
        $pickupPointData = new PickupPointData();

        // Nastavení nových hodnot
        $pickupPointData->id = "456";
        $pickupPointData->name = "Updated Pickup Point";
        $pickupPointData->street = "Updated Street 456";
        $pickupPointData->city = "Updated City";
        $pickupPointData->openingHours = [
            "Středa" => "10:00 - 18:00"
        ];
        $pickupPointData->latitude = 51.0000;
        $pickupPointData->longitude = 15.0000;
        $pickupPointData->googleMapKey = "updatedGoogleMapKey";

        // Ověření nových hodnot
        $this->assertSame("456", $pickupPointData->id);
        $this->assertSame("Updated Pickup Point", $pickupPointData->name);
        $this->assertSame("Updated Street 456", $pickupPointData->street);
        $this->assertSame("Updated City", $pickupPointData->city);
        $this->assertSame(["Středa" => "10:00 - 18:00"], $pickupPointData->openingHours);
        $this->assertSame(51.0000, $pickupPointData->latitude);
        $this->assertSame(15.0000, $pickupPointData->longitude);
        $this->assertSame("updatedGoogleMapKey", $pickupPointData->googleMapKey);
    }
}
