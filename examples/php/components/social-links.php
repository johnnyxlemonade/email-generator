<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\SocialLinks;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\SocialLink;
use Lemonade\EmailGenerator\ValueObject\Translation;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Social links composition')
    ->add(new SocialLinks([
        new SocialLink(new Translation('emails.social_community'), 'https://community.example.com/northstar'),
        new SocialLink('Product updates', 'https://updates.example.com/northstar'),
        new SocialLink('Engineering notes', 'https://engineering.example.com/northstar'),
    ]))
    ->render();
