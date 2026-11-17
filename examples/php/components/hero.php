<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Hero;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\ImageSource;
use Lemonade\EmailGenerator\ValueObject\Translation;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Hero composition')
    ->add(new Hero(
        heading: new Translation('emails.hero_heading'),
        description: new Translation('emails.hero_description'),
        image: new ImageSource(
            url: 'https://placehold.co/944x400/png?text=Northstar+Workspace',
            alt: new Translation('emails.hero_image_alt'),
        ),
        button: new Button(new Translation('emails.hero_action'), 'https://example.com/northstar'),
    ))
    ->render();
