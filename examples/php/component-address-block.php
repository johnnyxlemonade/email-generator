<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\Address;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'AddressBlock composition')
    ->add(new Columns(
        first: [
            new Heading('Company address', level: 3),
            new AddressBlock(new Address(
                name: 'Northstar Studio GmbH',
                street: 'Friedrichstraße 42',
                city: 'Berlin',
                postalCode: '10117',
                country: 'Germany',
                companyId: 'HRB 123456',
                taxId: 'DE123456789',
                email: 'office@example.com',
            )),
        ],
        second: [
            new Heading('Delivery address', level: 3),
            new AddressBlock(new Address(
                name: 'Jordan Lee',
                street: '125 Market Street',
                city: 'San Francisco, CA',
                postalCode: '94105',
                country: 'United States',
                phone: '+1 415 555 0182',
            )),
        ],
    ))
    ->render();
