<?php

namespace Lemonade\EmailGenerator\Tests\Services;

use Lemonade\EmailGenerator\DTO\ShippingData;
use Lemonade\EmailGenerator\Models\Shipping;
use Lemonade\EmailGenerator\Services\ShippingService;
use PHPUnit\Framework\TestCase;

class ShippingServiceTest extends TestCase
{
    public function testCreateShippingReturnsShippingInstance(): void
    {
        // initialize ShippingData with data
        $data = new ShippingData(
            name: 'Express Shipping',
            price: 10.99,
            display: true
        );

        // Creating an instance of ShippingService and calling createShipping
        $service = new ShippingService();
        $shipping = $service->createShipping($data);

        // Validating that the returned object is an instance of Shipping
        $this->assertInstanceOf(Shipping::class, $shipping);

        // Verifying that the data in the Shipping object matches the data from the DTO
        $this->assertSame('Express Shipping', $shipping->getName());
        $this->assertSame(10.99, $shipping->getPrice());
        $this->assertTrue($shipping->shouldDisplay());
    }
}
