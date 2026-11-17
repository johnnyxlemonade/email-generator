<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;
use Lemonade\EmailGenerator\ValueObject\ImageSource;

/** @var EmailGenerator $emails */

$acceptUrl = 'https://example.com/invitations/team-northstar/accept?token=demo-44aa';
$declineUrl = 'https://example.com/invitations/team-northstar/decline?token=demo-44aa';

return $emails->create(
    locale: 'en',
    title: 'Maya invited you to Northstar Product',
    preheader: 'Join the Northstar Product workspace as an Editor.',
)->add(new Group(
    children: [new Columns(
        first: [new Signature(
            new Contact('Maya Chen', 'Product Operations Lead', 'Northstar Product'),
            new ImageSource('https://placehold.co/224x224/dbeafe/1e3a8a/png?text=MC', 'Maya Chen'),
        )],
        second: [
            new Heading('You are invited', level: 2),
            new Text('Maya Chen invited you to collaborate in the Northstar Product workspace.'),
        ],
        gap: 24,
        firstWidth: 260,
    )],
    backgroundColor: new Color('eff6ff'),
    padding: 24,
    borderColor: new Color('bfdbfe'),
))->add(new Quote(
    content: 'We are bringing product decisions, research notes and launch plans into one place. I would love your help shaping the customer onboarding project.',
    attribution: 'Maya Chen',
))->add(KeyValue::listing(
    new KeyValue('Workspace', 'Northstar Product'),
    new KeyValue('Your role', new RichText(TextRun::strong('Editor'))),
    new KeyValue('Invited by', 'Maya Chen · maya.chen@example.com'),
    new KeyValue('Invitation expires', 'September 14, 2027'),
))->add(new ButtonGroup([
    new Button('Accept invitation', $acceptUrl),
    new Button('Decline', $declineUrl, ButtonVariant::Secondary),
]))
    ->heading('As an Editor, you can', level: 2)
    ->list([
        'Create and organize projects, pages and decisions.',
        'Comment on work and mention teammates in discussions.',
        'Invite project guests after a workspace admin approves them.',
    ])
    ->divider()
    ->text(new RichText(
        'This invitation was sent to ',
        TextRun::strong('sam.rivera@example.com'),
        '. Sign in with that address to join the workspace.',
    ))
    ->render();
