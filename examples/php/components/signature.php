<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\Contact;
use Lemonade\EmailGenerator\ValueObject\ImageSource;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Signature composition')
    ->add(
        new Signature(
            contact: new Contact(
                name: 'Maya Chen',
                position: 'Customer Success Lead',
                organization: 'Northstar Studio',
                email: 'maya.chen@example.com',
                phone: '+1 415 555 0148',
            ),
            image: new ImageSource(
                url: 'https://placehold.co/112x112/png?text=MC',
                alt: 'Portrait of Maya Chen',
            ),
        ),
        new Signature(new Contact(
            name: 'Daniel Novak',
            position: 'Solutions Architect',
            organization: 'Northstar Studio',
            email: 'daniel.novak@example.com',
        )),
    )
    ->render();
