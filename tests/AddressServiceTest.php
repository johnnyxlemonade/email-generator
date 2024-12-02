<?php

namespace Lemonade\EmailGenerator\Tests;

// tests/AddressServiceTest.php
use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\DTO\AddressDTO;
use Lemonade\EmailGenerator\Services\AddressService;
use Lemonade\EmailGenerator\Models\Address;

class AddressServiceTest extends TestCase
{
    public function testGetAddressCreatesCorrectAddress()
    {
        // Arrange
        $addressData = new AddressDTO([
            "addressCompanyId" => "CZ12345678",
            "addressCompanyVatId" => "CZ87654321",
            "addressCompanyName" => "Firma XYZ",
            "addressAlias" => "Sídlo",
            "addressName" => "Josef Novák",
            "addressStreet" => "Ulice 1234/56",
            "addressPostcode" => "12345",
            "addressCity" => "Praha",
            "addressCountry" => "CZ",
            "addressPhone" => "123456789",
            "addressEmail" => "josef.novak@example.com"
        ]);

        $addressService = new AddressService();

        // Act
        $address = $addressService->getAddress($addressData);

        // Assert
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals("CZ12345678", $address->getCompanyId());
        $this->assertEquals("Josef Novák", $address->getAddressName());
        $this->assertEquals("Praha", $address->getAddressCity());
        $this->assertEquals("josef.novak@example.com", $address->getAddressEmail());
    }

    public function testAddressCopyCreatesIdenticalAddress()
    {
        // Arrange
        $addressData = new AddressDTO([
            "CZ12345678",
            "CZ87654321",
            "Firma XYZ",
            "Sídlo",
            "Josef Novák",
            "Ulice 1234/56",
            "12345",
            "Praha",
            "CZ",
            "123456789",
            "josef.novak@example.com"
        ]);
        $addressService = new AddressService();
        $address = $addressService->getAddress($addressData);

        // Act
        $copiedAddress = $address->copy();

        // Assert
        $this->assertInstanceOf(Address::class, $copiedAddress);
        $this->assertEquals($address->getCompanyId(), $copiedAddress->getCompanyId());
        $this->assertEquals($address->getAddressName(), $copiedAddress->getAddressName());
        $this->assertEquals($address->getAddressCity(), $copiedAddress->getAddressCity());
        $this->assertEquals($address->getAddressEmail(), $copiedAddress->getAddressEmail());
    }
}

