<?php

namespace Lemonade\EmailGenerator\Tests\Model;

use Lemonade\EmailGenerator\Models\PickupPoint;
use PHPUnit\Framework\TestCase;

class PickupPointTest extends TestCase
{
    /**
     * Testuje základní funkčnost getterů
     *
     * Ověřuje, že po vytvoření instance třídy PickupPoint
     * jsou všechny vlastnosti správně nastaveny a přístupné.
     */
    public function testGetters(): void
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

        $pickupPoint = new PickupPoint($id, $name, $street, $city, $openingHours, $latitude, $longitude, $googleMapKey);

        $this->assertSame($id, $pickupPoint->getId());
        $this->assertSame($name, $pickupPoint->getName());
        $this->assertSame($street, $pickupPoint->getStreet());
        $this->assertSame($city, $pickupPoint->getCity());
        $this->assertSame($openingHours, $pickupPoint->getOpeningHours());
        $this->assertSame($latitude, $pickupPoint->getLatitude());
        $this->assertSame($longitude, $pickupPoint->getLongitude());
        $this->assertSame($googleMapKey, $pickupPoint->getGoogleMapKey());
    }

    /**
     * Testuje metodu getFullAddress
     *
     * Ověřuje, že metoda getFullAddress vrací správnou formátovanou adresu.
     */
    public function testGetFullAddress(): void
    {
        $pickupPoint = new PickupPoint(null, "Test Pickup Point", "Test Street 123", "Test City");

        $expectedAddress = "Test Street 123, Test City";
        $this->assertSame($expectedAddress, $pickupPoint->getFullAddress());

        // Testuje, že metoda vrátí null, pokud chybí adresa i město
        $pickupPointWithNoAddress = new PickupPoint();
        $this->assertNull($pickupPointWithNoAddress->getFullAddress());
    }

    /**
     * Testuje metodu getFormattedOpeningHours s vyplněnou otevírací dobou
     *
     * Ověřuje, že metoda getFormattedOpeningHours vrací správně formátovaný řetězec.
     */
    public function testGetFormattedOpeningHoursWithHours(): void
    {
        $openingHours = [
            "Pondělí" => "9:00 - 17:00",
            "Úterý" => "9:00 - 17:00"
        ];
        $pickupPoint = new PickupPoint(null, "Test Pickup Point", null, null, $openingHours);

        $expectedFormattedHours = "Pondělí: 9:00 - 17:00<br>Úterý: 9:00 - 17:00";
        $this->assertSame($expectedFormattedHours, $pickupPoint->getFormattedOpeningHours());
    }

    /**
     * Testuje metodu getFormattedOpeningHours bez vyplněné otevírací doby
     *
     * Ověřuje, že pokud není žádná otevírací doba, metoda getFormattedOpeningHours vrací null.
     */
    public function testGetFormattedOpeningHoursWithoutHours(): void
    {
        $pickupPoint = new PickupPoint();

        // Ověřuje, že pokud není žádná otevírací doba, metoda vrací null
        $this->assertNull($pickupPoint->getFormattedOpeningHours());
    }

    /**
     * Testuje, že pokud žádná z hodnot v PickupPoint není nastavena,
     * jsou gettery správně inicializovány s hodnotou null nebo prázdným polem.
     */
    public function testEmptyInitialization(): void
    {
        $pickupPoint = new PickupPoint();

        $this->assertNull($pickupPoint->getId());
        $this->assertNull($pickupPoint->getName());
        $this->assertNull($pickupPoint->getStreet());
        $this->assertNull($pickupPoint->getCity());
        $this->assertSame([], $pickupPoint->getOpeningHours());
        $this->assertNull($pickupPoint->getLatitude());
        $this->assertNull($pickupPoint->getLongitude());
        $this->assertNull($pickupPoint->getGoogleMapKey());
    }
}

