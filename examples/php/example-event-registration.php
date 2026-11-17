<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$eventUrl = 'https://example.com/events/designing-better-services';
$calendarUrl = 'https://example.com/events/designing-better-services/calendar';
$registrationUrl = 'https://example.com/registrations/WS-2027-0418';
$venueUrl = 'https://example.com/venues/riverside-studio';
$supportUrl = 'https://example.com/events/support?registration=WS-2027-0418';
$border = new Color('ddd6fe');
$softViolet = new Color('f5f3ff');
$softSlate = new Color('f8fafc');

return $emails->create(
    locale: 'en',
    title: 'Registration confirmed — Designing Better Services',
    preheader: 'You are registered for Designing Better Services on April 18, 2027.',
)->add(new Group(
    children: [new Columns(
        first: [new Image(
            'https://placehold.co/144x144/312e81/ffffff/png?text=FC',
            'Fieldwork Collective logo',
            width: 72,
            height: 72,
        )],
        second: [new Text(new RichText(
            TextRun::strong('FIELDWORK COLLECTIVE'),
            "\nLearning experiences for thoughtful teams",
        ))],
        gap: 24,
    )],
    backgroundColor: $softSlate,
    padding: 16,
    borderColor: new Color('e2e8f0'),
))->image(
    url: 'https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&w=1200&q=80',
    alt: 'Workshop participants collaborating around a table',
    width: 536,
    height: 214,
)->add(new Badge('Registration confirmed', BadgeVariant::Success))
    ->heading('Your place is reserved')
    ->text(new RichText(
        'Hi Maya,',
        "\n\n",
        'you are confirmed for ',
        TextRun::strong('Designing Better Services'),
        '. We are looking forward to a practical day of research, mapping and collaborative prototyping.',
    ))
    ->add(new Group(
        children: [
            new Heading('Designing Better Services', level: 2),
            new Text(new RichText(
                TextRun::strong('A hands-on workshop for product and service teams'),
                "\nSaturday, April 18, 2027 · 09:30–16:30",
            )),
        ],
        backgroundColor: $softViolet,
        padding: 24,
        borderColor: $border,
    ))
    ->add(new Steps(['Registered', 'Prepare for the day', 'Attend the workshop'], currentStep: 2))
    ->heading('Registration details', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Registration', 'WS-2027-0418'),
        new KeyValue('Attendee', 'Maya Chen'),
        new KeyValue('Ticket', 'Professional · In person'),
        new KeyValue('Language', 'English'),
    ))
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Date and time', level: 3),
                new Text(new RichText(
                    TextRun::strong('Saturday, April 18, 2027'),
                    "\n09:30–16:30 CEST",
                    "\nDoors open at 09:00",
                )),
            ],
            backgroundColor: $softSlate,
            padding: 16,
        )],
        second: [new Group(
            children: [
                new Heading('Venue', level: 3),
                new Text(new RichText(
                    TextRun::strong('Riverside Studio'),
                    "\n12 Foundry Lane",
                    "\nPrague 7, Czech Republic",
                    "\n",
                    TextRun::link('Venue and directions', $venueUrl),
                )),
            ],
            backgroundColor: $softSlate,
            padding: 16,
        )],
        gap: 16,
    ))
    ->heading('Workshop programme', level: 2)
    ->table(
        headers: ['Time', 'Session'],
        rows: [
            ['09:30', new RichText(TextRun::strong('Welcome and framing'), "\nMeet the cohort and define the day’s challenge")],
            ['10:15', new RichText(TextRun::strong('Research signals'), "\nTurn observations into useful evidence")],
            ['11:45', new RichText(TextRun::strong('Service mapping'), "\nConnect customer needs with operational reality")],
            ['13:30', new RichText(TextRun::strong('Prototype lab'), "\nBuild and test a service moment in small teams")],
            ['15:45', new RichText(TextRun::strong('Review and next steps'), "\nShare outcomes and plan what happens Monday")],
        ],
    )
    ->text(new RichText(
        TextRun::emphasis('Coffee, refreshments and a seasonal lunch are included.'),
        ' Please note dietary requirements in your registration by April 10.',
    ))
    ->add(new ButtonGroup(
        buttons: [
            new Button('Add to calendar', $calendarUrl),
            new Button('Manage registration', $registrationUrl, ButtonVariant::Secondary),
        ],
        gap: 12,
        alignment: HorizontalAlignment::Center,
    ))
    ->heading('Before you arrive', level: 2)
    ->add(new Group(
        children: [new BulletList([
            'Bring a laptop or tablet for the prototype exercise; chargers and Wi-Fi are provided.',
            'Arrive between 09:00 and 09:20 for check-in, coffee and an informal introduction.',
            'The venue is step-free. Tell us in advance about any access or dietary requirements.',
            new RichText('Review the short ', TextRun::link('participant briefing', $eventUrl), ' before the workshop.'),
        ])],
        backgroundColor: $softViolet,
        padding: 20,
        borderColor: $border,
    ))
    ->heading('Your facilitator', level: 2)
    ->add(new Columns(
        first: [new Image(
            'https://placehold.co/320x320/e0e7ff/312e81/png?text=Elena+M.',
            'Portrait placeholder for facilitator Elena Martín',
            width: 160,
            height: 160,
        )],
        second: [
            new Heading('Elena Martín', level: 3),
            new Text('Service designer and facilitator with twelve years of experience helping public-interest and technology teams turn research into practical change.'),
            new Text(new RichText(TextRun::link('View facilitator profile', 'https://example.com/team/elena-martin'))),
        ],
        gap: 24,
    ))
    ->divider(new Color('c4b5fd'), thickness: 2)
    ->add(new Group(
        children: [
            new Heading('Questions before the event?', level: 2),
            new Text(new RichText(
                'Events team',
                "\nevents@example.com · +420 555 014 220",
                "\nMonday–Friday, 09:00–17:00 CEST",
                "\n\n",
                TextRun::link('Open the attendee support centre', $supportUrl),
            )),
        ],
        backgroundColor: $softSlate,
        padding: 20,
        borderColor: new Color('e2e8f0'),
    ))
    ->spacer(8)
    ->text(new RichText(
        TextRun::strong('FIELDWORK COLLECTIVE'),
        "\n12 Foundry Lane, Prague 7",
        "\nRegistration WS-2027-0418 · This is a fictional demonstration event.",
    ))
    ->text(new RichText(
        TextRun::link('Event page', $eventUrl),
        ' · ',
        TextRun::link('Code of conduct', 'https://example.com/events/code-of-conduct'),
        ' · ',
        TextRun::link('Privacy', 'https://example.com/privacy'),
    ))
    ->render();
