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

$consultationUrl = 'https://example.com/konzultace/NA-2027-0918';
$rescheduleUrl = 'https://example.com/konzultace/NA-2027-0918/zmena-terminu';
$websiteUrl = 'https://example.com/northstar-advisory';

return $emails->create(
    locale: 'cs',
    title: 'Potvrzení strategické konzultace',
    preheader: 'Termín, program a praktické informace ke konzultaci 24. září.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR ADVISORY'),
    "\nStrategie digitálních produktů",
))->divider(new Color('0f766e'), thickness: 3)
    ->text('Dobrý den, pane Nováku,')
    ->heading('Vaše strategická konzultace je potvrzena')
    ->text('děkuji za důvěru a za podklady, které jste mi poslal. Termín máme závazně rezervovaný a během devadesáti minut společně připravíme konkrétní priority pro další rozvoj vašeho zákaznického portálu.')
    ->add(new Badge('Termín potvrzen', BadgeVariant::Success))
    ->add(new Group(
        children: [
            new Heading('Podrobnosti konzultace', level: 2),
            KeyValue::listing(
                new KeyValue('Číslo rezervace', new RichText(TextRun::strong('NA-2027-0918'))),
                new KeyValue('Datum', 'pátek 24. září 2027'),
                new KeyValue('Čas', '10:00–11:30'),
                new KeyValue('Téma', 'Prioritizace produktového roadmapu pro B2B portál'),
                new KeyValue('Forma', 'Osobně · konzultační studio Orion'),
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
                    new Heading('Kde se potkáme', level: 3),
                    new Text(new RichText(
                        TextRun::strong('Northstar Studio'),
                        "\nPobřežní 620/3",
                        "\nPraha 8 – Karlín",
                        "\n2. patro, recepce vlevo",
                    )),
                    new Text('Na recepci stačí uvést číslo rezervace NA-2027-0918.'),
                ],
                backgroundColor: new Color('f8fafc'),
                padding: 16,
                borderColor: new Color('e2e8f0'),
            ),
        ],
        second: [
            new Group(
                children: [
                    new Heading('Co si připravit', level: 3),
                    new BulletList([
                        'aktuální produktový roadmap na příští dvě čtvrtletí,',
                        'dva až tři poznatky z posledních rozhovorů se zákazníky,',
                        'seznam rozhodnutí, u kterých potřebujete sladit vedení týmu.',
                    ]),
                    new Text('Podklady není nutné předem upravovat do prezentace.'),
                ],
                backgroundColor: new Color('fffbeb'),
                padding: 16,
                borderColor: new Color('fde68a'),
            ),
        ],
        gap: 16,
    ))
    ->heading('Jak bude setkání probíhat', level: 2)
    ->text('Nejprve si potvrdíme obchodní cíl a omezení projektu. Hlavní část věnujeme společnému workshopu nad roadmapou; závěr využijeme k výběru tří priorit a domluvě konkrétních dalších kroků. Do následujícího pracovního dne vám pošlu stručný zápis včetně doporučení.')
    ->add(new ButtonGroup([
        new Button('Zobrazit detail konzultace', $consultationUrl),
        new Button('Požádat o změnu termínu', $rescheduleUrl, ButtonVariant::Secondary),
    ]))
    ->text('Pokud se před setkáním objeví nová otázka nebo budete chtít doplnit podklady, napište mi. Ráda je do programu zahrnu, aby byl společný čas co nejpraktičtější.')
    ->text("Těším se na setkání.\n\nS pozdravem")
    ->add(new Signature(new Contact(
        name: 'Klára Veselá',
        position: 'Seniorní konzultantka digitální strategie',
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
                city: 'Praha 8 – Karlín',
                postalCode: '186 00',
                country: 'Česká republika',
                companyId: '073 84 521',
                taxId: 'CZ07384521',
                email: 'info@example.com',
                phone: '+420 222 581 940',
            )),
            new Text(new RichText(
                'Web: ',
                TextRun::link('example.com/northstar-advisory', $websiteUrl),
                "\nTento e-mail byl odeslán v souvislosti s rezervací konzultace NA-2027-0918.",
            )),
        ],
        backgroundColor: new Color('f8fafc'),
        padding: 16,
        borderColor: new Color('e2e8f0'),
    ))
    ->render();
