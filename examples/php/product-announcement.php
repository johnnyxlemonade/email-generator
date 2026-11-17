<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Hero;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\SocialLinks;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;
use Lemonade\EmailGenerator\ValueObject\ImageSource;
use Lemonade\EmailGenerator\ValueObject\SocialLink;

/** @var EmailGenerator $emails */

$productUrl = 'https://example.com/northstar/workspaces';

return $emails->create(
    locale: 'en',
    title: 'Introducing Northstar Workspaces',
    preheader: 'A calmer, shared home for projects, decisions and team knowledge.',
)->add(new Hero(
    heading: 'Introducing Northstar Workspaces',
    description: 'Bring project plans, decisions and living documentation together in one focused workspace your whole team can trust.',
    image: new ImageSource(
        url: 'https://placehold.co/944x440/png?text=Northstar+Workspaces',
        alt: 'Northstar Workspaces product overview',
    ),
    button: new Button('Explore Workspaces', $productUrl),
))->text(new RichText(
    'Hi Morgan,',
    "\n\n",
    'Teams told us that important context was scattered across too many tools. We built Workspaces to give every initiative a clear home—from the first brief to the final decision.',
))->heading('Designed for work that keeps moving', level: 2)
    ->list([
        'Keep goals, owners and current status visible without maintaining a separate report.',
        'Capture decisions beside the work they affect, so new contributors can catch up quickly.',
        'Share a polished read-only view with clients and partners when the work is ready.',
    ])
    ->add(new Alert(
        content: 'Existing Northstar customers can create their first Workspace today at no additional cost during the early-access period.',
        heading: 'Early access is open',
        variant: AlertVariant::Success,
    ))
    ->text(new RichText(
        'We would love to hear what your team builds. ',
        TextRun::link('Read the launch guide', 'https://example.com/northstar/workspaces/guide'),
        ' or reply to this email with your questions.',
    ))
    ->button('Create your first Workspace', $productUrl)
    ->divider(new Color('cbd5e1'))
    ->add(new Signature(
        contact: new Contact(
            name: 'Maya Chen',
            position: 'VP of Product',
            organization: 'Northstar',
            email: 'maya.chen@example.com',
        ),
        image: new ImageSource(
            url: 'https://placehold.co/224x224/png?text=MC',
            alt: 'Maya Chen',
        ),
    ))
    ->add(new SocialLinks([
        new SocialLink('Community', 'https://community.example.com/northstar'),
        new SocialLink('Product updates', 'https://updates.example.com/northstar'),
        new SocialLink('Engineering', 'https://engineering.example.com/northstar'),
    ]))
    ->divider(new Color('e2e8f0'))
    ->add(new AddressBlock(new Address(
        name: 'Northstar Software, Inc.',
        street: '240 Market Street',
        city: 'San Francisco, CA',
        postalCode: '94105',
        country: 'United States',
        email: 'hello@example.com',
    )))
    ->text('You are receiving this product announcement because you manage a Northstar workspace.')
    ->render();
