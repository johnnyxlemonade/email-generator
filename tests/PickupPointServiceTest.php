<?php

namespace Lemonade\EmailGenerator\Tests;

use Lemonade\EmailGenerator\DTO\PickupPointData;
use Lemonade\EmailGenerator\Factories\PickupPointFactory;
use Lemonade\EmailGenerator\Models\PickupPoint;
use Lemonade\EmailGenerator\Services\PickupPointService;
use PHPUnit\Framework\TestCase;

class PickupPointServiceTest extends TestCase
{
    private PickupPointService $pickupPointService;

    protected function setUp(): void
    {
        $this->pickupPointService = new PickupPointService();
    }

    public function testCreatePickupPoint(): void
    {
        $data = new PickupPointData(
            id: "123",
            name: "Test Pickup Point",
            street: "123 Test Street",
            city: "Test City",
            openingHours: ["Mon" => "9:00-18:00"],
            latitude: 50.0,
            longitude: 14.0,
            googleMapKey: "TestKey"
        );

        $pickupPoint = $this->pickupPointService->createPickupPoint($data);

        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertEquals("123", $pickupPoint->getId());
        $this->assertEquals("Test Pickup Point", $pickupPoint->getName());
        $this->assertEquals("123 Test Street", $pickupPoint->getStreet());
        $this->assertEquals("Test City", $pickupPoint->getCity());
    }
}
