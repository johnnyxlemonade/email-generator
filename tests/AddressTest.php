<?php

namespace Lemonade\EmailGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\Models\Address;
use Lemonade\EmailGenerator\DTO\AddressDTO;

class AddressTest extends TestCase
{
    public function testAddressInitializationWithNullData()
    {
        $address = new Address();
        $this->assertInstanceOf(Address::class, $address);
        $this->assertInstanceOf(AddressDTO::class, $address->getAddress());
    }

    public function testAddressInitializationWithData()
    {
        $data = new AddressDTO([
            'addressCompanyName' => 'Firma XYZ',
            'addressStreet' => 'Ulice 1234/56',
            'addressCity' => 'Praha',
        ]);

        $address = new Address($data);

        $this->assertEquals('Firma XYZ', $address->getCompanyName());
        $this->assertEquals('Ulice 1234/56', $address->getAddressStreet());
        $this->assertEquals('Praha', $address->getAddressCity());
    }

    public function testSetAddress()
    {
        $address = new Address();
        $data = new AddressDTO([
            'addressCompanyName' => 'Firma ABC',
            'addressStreet' => 'Nová ulice 789',
            'addressCity' => 'Brno',
        ]);

        $address->setAddress($data);

        $this->assertEquals('Firma ABC', $address->getCompanyName());
        $this->assertEquals('Nová ulice 789', $address->getAddressStreet());
        $this->assertEquals('Brno', $address->getAddressCity());
    }

    public function testGetterAndSetterMethods()
    {
        $address = new Address();

        $address->setCompanyId('CZ12345678');
        $this->assertEquals('CZ12345678', $address->getCompanyId());

        $address->setCompanyVatId('CZ87654321');
        $this->assertEquals('CZ87654321', $address->getCompanyVatId());

        $address->setCompanyName('Firma ABC');
        $this->assertEquals('Firma ABC', $address->getCompanyName());

        $address->setAddressAlias('Sídlo');
        $this->assertEquals('Sídlo', $address->getAddressAlias());

        $address->setAddressName('Josef Novák');
        $this->assertEquals('Josef Novák', $address->getAddressName());

        $address->setAddressStreet('Ulice 1234/56');
        $this->assertEquals('Ulice 1234/56', $address->getAddressStreet());

        $address->setAddressPostcode('12345');
        $this->assertEquals('12345', $address->getAddressPostcode());

        $address->setAddressCity('Praha');
        $this->assertEquals('Praha', $address->getAddressCity());

        $address->setAddressCountry('CZ');
        $this->assertEquals('Česká republika', $address->getAddressCountry());

        $address->setAddressPhone('123456789');
        $this->assertEquals('123456789', $address->getAddressPhone());

        $address->setAddressEmail('josef.novak@example.com');
        $this->assertEquals('josef.novak@example.com', $address->getAddressEmail());
    }

    public function testCopyMethod()
    {
        $data = new AddressDTO([
            'addressCompanyName' => 'Firma XYZ',
            'addressStreet' => 'Ulice 1234/56',
            'addressCity' => 'Praha',
        ]);

        $address = new Address($data);
        $copy = $address->copy();

        $this->assertInstanceOf(Address::class, $copy);
        $this->assertEquals($address->getCompanyName(), $copy->getCompanyName());
        $this->assertEquals($address->getAddressStreet(), $copy->getAddressStreet());
        $this->assertEquals($address->getAddressCity(), $copy->getAddressCity());
        $this->assertNotSame($address, $copy); // Ověření, že se jedná o odlišné instance
    }

    public function testToArrayMethod()
    {
        $data = new AddressDTO([
            'addressCompanyName' => 'Firma XYZ',
            'addressStreet' => 'Ulice 1234/56',
            'addressCity' => 'Praha',
            'addressCountry' => 'CZ',
            'addressEmail' => 'josef.novak@example.com'
        ]);

        $address = new Address($data);
        $arrayData = $address->toArray();

        $this->assertIsArray($arrayData);
        $this->assertArrayHasKey('addressCompanyName', $arrayData);
        $this->assertArrayHasKey('addressStreet', $arrayData);
        $this->assertArrayHasKey('addressCity', $arrayData);
        $this->assertArrayHasKey('addressCountry', $arrayData);
        $this->assertArrayHasKey('addressEmail', $arrayData);

        $this->assertEquals('Firma XYZ', $arrayData['addressCompanyName']);
        $this->assertEquals('Ulice 1234/56', $arrayData['addressStreet']);
        $this->assertEquals('Praha', $arrayData['addressCity']);
        $this->assertEquals('CZ', $arrayData['addressCountry']);
        $this->assertEquals('josef.novak@example.com', $arrayData['addressEmail']);
    }
}
