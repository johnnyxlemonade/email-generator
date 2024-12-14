<?php

namespace Lemonade\EmailGenerator\Tests\Services;

use Lemonade\EmailGenerator\DTO\AddressData;
use Lemonade\EmailGenerator\Models\Address;
use Lemonade\EmailGenerator\Services\AddressService;
use PHPUnit\Framework\TestCase;

class AddressServiceTest extends TestCase
{
    public function testGetAddressReturnsAddressInstance(): void
    {
        // Initialize AddressDatacl with data
        $dto = new AddressData([
            'addressName' => 'John Doe',
            'addressStreet' => '123 Main St',
            'addressCity' => 'Springfield',
            'addressCountry' => 'USA',
        ]);

        // Create an AddressService instance and call getAddress
        $service = new AddressService();
        $address = $service->getAddress($dto);

        // Verify that the returned object is an instance of Address
        $this->assertInstanceOf(Address::class, $address);

        // Verify that the data in the Address object matches the data from the DTO
        $this->assertSame('John Doe', $address->getAddressName());
        $this->assertSame('123 Main St', $address->getAddressStreet());
        $this->assertSame('Springfield', $address->getAddressCity());
        $this->assertSame('USA', $address->getAddressCountry());
    }
}
