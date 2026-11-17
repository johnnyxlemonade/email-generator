<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;

/** @var EmailGenerator $emails */

$consultationUrl = 'https://example.com/consultations/NA-2027-0918';
$rescheduleUrl = 'https://example.com/consultations/NA-2027-0918/reschedule';
$websiteUrl = 'https://example.com/northstar-advisory';

return $emails->create(
    locale: 'en',
    title: 'Strategic consultation confirmation',
    preheader: 'Schedule, agenda and practical information for your September 24 consultation.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR ADVISORY'),
    "\nDigital product strategy",
))->divider(new Color('0f766e'), thickness: 3)
    ->text('Hello Mr Novak,')
    ->heading('Your strategic consultation is confirmed')
    ->text('Thank you for your trust and for the materials you sent. Your appointment is reserved, and during our ninety-minute session we will establish clear priorities for the next stage of your customer portal.')
    ->add(new Badge('Appointment confirmed', BadgeVariant::Success))
    ->add(new Group(
        children: [
            new Heading('Consultation details', level: 2),
            KeyValue::listing(
                new KeyValue('Booking number', new RichText(TextRun::strong('NA-2027-0918'))),
                new KeyValue('Date', 'Friday, September 24, 2027'),
                new KeyValue('Time', '10:00–11:30'),
                new KeyValue('Topic', 'Product roadmap prioritization for a B2B portal'),
                new KeyValue('Format', 'In person · Orion consultation studio'),
            ),
        ],
        backgroundColor: new Color('f0fdfa'),
        padding: 20,
        borderColor: new Color('99f6e4'),
    ))
    ->add(new Columns(
        first: [
            new Group(
                children: [
                    new Heading('Where we will meet', level: 3),
                    new Text(new RichText(
                        TextRun::strong('Northstar Studio'),
                        "\nPobřežní 620/3",
                        "\nPrague 8 – Karlin",
                        "\nSecond floor, reception on the left",
                    )),
                    new Text('Please give booking number NA-2027-0918 at reception.'),
                ],
                backgroundColor: new Color('f8fafc'),
                padding: 16,
                borderColor: new Color('e2e8f0'),
            ),
        ],
        second: [
            new Group(
                children: [
                    new Heading('What to prepare', level: 3),
                    new BulletList([
                        'your current product roadmap for the next two quarters,',
                        'two or three insights from recent customer interviews,',
                        'a list of decisions that require leadership alignment.',
                    ]),
                    new Text('You do not need to turn the materials into a presentation beforehand.'),
                ],
                backgroundColor: new Color('fffbeb'),
                padding: 16,
                borderColor: new Color('fde68a'),
            ),
        ],
        gap: 16,
    ))
    ->heading('How the session will work', level: 2)
    ->text('We will begin by confirming the business objective and project constraints. The main part will be a collaborative roadmap workshop, followed by selecting three priorities and agreeing on concrete next steps. I will send you a concise summary with recommendations by the next business day.')
    ->add(new ButtonGroup([
        new Button('View consultation details', $consultationUrl),
        new Button('Request a new time', $rescheduleUrl, ButtonVariant::Secondary),
    ]))
    ->text('If a new question comes up or you would like to add more materials before the session, please write to me. I will include them in the agenda so our time together is as practical as possible.')
    ->text("I look forward to meeting you.\n\nBest regards")
    ->add(new Signature(new Contact(
        name: 'Klára Veselá',
        position: 'Senior Digital Strategy Consultant',
        organization: 'Northstar Advisory',
        email: 'klara.vesela@example.com',
        phone: '+420 777 245 810',
    )))
    ->divider(new Color('cbd5e1'))
    ->add(new Group(
        children: [
            new AddressBlock(new Address(
                name: 'Northstar Advisory s.r.o.',
                street: 'Pobřežní 620/3',
                city: 'Prague 8 – Karlin',
                postalCode: '186 00',
                country: 'Czech Republic',
                companyId: '073 84 521',
                taxId: 'CZ07384521',
                email: 'info@example.com',
                phone: '+420 222 581 940',
            )),
            new Text(new RichText(
                'Web: ',
                TextRun::link('example.com/northstar-advisory', $websiteUrl),
                "\nThis email was sent in connection with consultation booking NA-2027-0918.",
            )),
        ],
        backgroundColor: new Color('f8fafc'),
        padding: 16,
        borderColor: new Color('e2e8f0'),
    ))
    ->render();
