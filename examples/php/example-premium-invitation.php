<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Hero;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\ImageSource;

/** @var EmailGenerator $emails */

$rsvpUrl = 'https://example.com/invitations/MA-2711/rsvp';
$declineUrl = 'https://example.com/invitations/MA-2711/decline';
$ivory = new Color('fffbeb');
$gold = new Color('d6b45a');
$ink = new Color('292524');

return $emails->create(
    locale: 'en',
    title: 'A private evening at Maison Aurelia',
    preheader: 'An invitation for Jordan Lee and guest — November 27 at Maison Aurelia.',
)->text('MAISON AURELIA · PARIS', alignment: HorizontalAlignment::Center)
    ->divider($gold, thickness: 2)
    ->add(new Hero(
        heading: 'The Art of the Table',
        description: 'An intimate evening of seasonal cuisine, contemporary craft and conversation hosted in the private salons of Maison Aurelia.',
        image: new ImageSource(
            'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1400&q=80',
            'An elegant dining room prepared for an intimate evening',
        ),
    ))
    ->add(new Badge('Private invitation', BadgeVariant::Warning))
    ->heading('Jordan, we would be delighted to welcome you', alignment: HorizontalAlignment::Center)
    ->text(
        'Creative director Amélie Laurent invites you and one guest to a private dinner marking the opening of the Maison’s winter collection.',
        alignment: HorizontalAlignment::Center,
    )
    ->add(new Group(
        children: [KeyValue::listing(
            new KeyValue('Date', 'Saturday, November 27, 2027'),
            new KeyValue('Arrival', '19:00 · Dinner at 20:00'),
            new KeyValue('Invitation', 'Jordan Lee and one guest'),
            new KeyValue('Reply by', new RichText(TextRun::strong('November 12, 2027'))),
        )],
        backgroundColor: $ivory,
        padding: 24,
        borderColor: $gold,
    ))
    ->add(new Quote(
        content: 'The most memorable objects begin with a conversation. This evening is an invitation to share both.',
        attribution: 'Amélie Laurent · Creative Director',
    ))
    ->heading('The evening', level: 2, alignment: HorizontalAlignment::Center)
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('19:00 · Salon', level: 3),
                new Text('Champagne reception and a private viewing of twelve commissioned pieces with the participating makers.'),
            ],
            backgroundColor: $ivory,
            padding: 18,
        )],
        second: [new Group(
            children: [
                new Heading('20:00 · Table', level: 3),
                new Text('A five-course seasonal menu by chef Luc Moreau, followed by coffee in the winter garden.'),
            ],
            backgroundColor: $ivory,
            padding: 18,
        )],
        gap: 16,
    ))
    ->add(new Group(
        children: [
            new Heading('Maison Aurelia', level: 3),
            new AddressBlock(new Address(
                name: 'Private Salons',
                street: '18 Rue de la Verrerie',
                city: 'Paris',
                postalCode: '75004',
                country: 'France',
                phone: '+33 1 84 80 27 11',
            )),
            new Text('Cocktail attire · Valet service is available from Rue du Renard.'),
        ],
        backgroundColor: new Color('f5f5f4'),
        padding: 20,
        borderColor: new Color('d6d3d1'),
    ))
    ->add(new ButtonGroup(
        buttons: [
            new Button('Accept invitation', $rsvpUrl),
            new Button('Unable to attend', $declineUrl, ButtonVariant::Secondary),
        ],
        alignment: HorizontalAlignment::Center,
    ))
    ->divider($ink)
    ->text(
        'Invitation MA-2711 · Please present this invitation when you arrive.',
        alignment: HorizontalAlignment::Center,
    )
    ->render();
