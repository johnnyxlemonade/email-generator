<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Examples;

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Composes example-specific care instructions from existing public components
 */
final readonly class OrderCareNote implements CompositionInterface
{
    /**
     * Returns a reusable care section without producing HTML or requiring a renderer
     *
     * @return iterable<ComponentInterface>
     */
    public function components(): iterable
    {
        yield new Heading('Ať vám dělají radost dlouho', level: 3);
        yield new BulletList([
            'Keramiku před prvním použitím opláchněte. Jemné odchylky glazury jsou přirozenou součástí ruční výroby.',
            'Len perte na 40 °C s podobnými barvami. S každým praním bude měkčí a příjemnější.',
            new RichText('Papírovou výplň můžete znovu použít nebo vytřídit. ', TextRun::emphasis('Děkujeme, že na to myslíte.')),
        ]);
    }
}

/** @var EmailGenerator $emails */

$orderNumber = 'DD-2026-10482';
$orderUrl = 'https://example.com/orders/' . $orderNumber;
$border = new Color('e2e8f0');
$softBackground = new Color('f8fafc');

$products = [
    [
        'name' => 'Keramický hrnek Ranní klid',
        'variant' => 'Slonová kost · 300 ml',
        'quantity' => 2,
        'unitPrice' => 44900,
        'imageUrl' => 'https://placehold.co/352x352/png?text=Ceramic+Mug',
        'imageWidth' => 176,
        'description' => 'Ručně glazovaný hrnek s pohodlným uchem. Každý kus má jemně odlišnou kresbu a vlastní charakter.',
    ],
    [
        'name' => 'Lněná utěrka Každý den',
        'variant' => 'Šalvějová · 50 × 70 cm',
        'quantity' => 1,
        'unitPrice' => 34900,
        'imageUrl' => 'https://placehold.co/416x416/png?text=Linen+Towel',
        'imageWidth' => 208,
        'description' => 'Přírodní len pro každodenní použití. Savý, odolný a příjemný na dotek — od ranní kávy až po večerní vaření.',
    ],
];
$money = static fn(int $minorUnits): string => number_format($minorUnits / 100, 2, ',', ' ') . ' Kč';
$subtotal = 0;
foreach ($products as $product) {
    $subtotal += $product['quantity'] * $product['unitPrice'];
}
$discount = 10000;
$shipping = 7900;
$total = $subtotal - $discount + $shipping;
// All amounts in this fictional order use one illustrative 21% VAT rate.
$taxBase = (int) round($total / 1.21);
$vat = $total - $taxBase;

$document = $emails->create(
    locale: 'cs',
    title: new Translation('order.title', ['number' => $orderNumber]),
    preheader: 'Děkujeme, Kláro. Platbu jsme přijali a vaši objednávku připravujeme. Celkem ' . $money($total) . '.',
)
    ->image('https://placehold.co/88x88/png?text=DD', 'Dobrý domov — logo domu', width: 44, height: 44)
    ->text(new RichText(TextRun::strong('DOBRÝ DOMOV'), "\nPoctivé věci pro každý den"))
    ->divider($border)
    ->spacer(8)
    ->heading(new Translation('order.heading'))
    ->text(new RichText(
        new Translation('order.greeting', ['name' => 'Kláro']),
        "\n\n",
        'objednávku ',
        TextRun::strong($orderNumber),
        ' jsme v pořádku přijali. Platba je potvrzená a teď už se o vše postaráme my.',
    ))
    ->add(new Badge('Zaplaceno · připravujeme', BadgeVariant::Success))
    ->text(new RichText(
        'Odeslání plánujeme na ',
        new TextRun('18. listopadu 2026', TextStyle::StrongEmphasis),
        '. Jakmile balíček předáme dopravci, pošleme vám číslo zásilky. Aktuální informace vždy najdete v ',
        TextRun::link('detailu objednávky', $orderUrl),
        '.',
    ))
    ->button('Zobrazit objednávku', $orderUrl)
    ->spacer(16)
    ->heading('Kam balíček pošleme', level: 2)
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Fakturační adresa', level: 3),
                new Text(new RichText(TextRun::strong('Klára Dvořáková'), "\nPod Lipami 18\n602 00 Brno\nČeská republika")),
            ],
            backgroundColor: $softBackground,
            padding: 16,
        )],
        second: [new Group(
            children: [
                new Heading('Doručovací adresa', level: 3),
                new Text(new RichText(TextRun::strong('Klára Dvořáková'), "\nAteliér Světlo & prostor\nKřížová 24, 2. patro\n603 00 Brno\nČeská republika")),
            ],
            backgroundColor: $softBackground,
            padding: 16,
        )],
        gap: 24,
    ))
    ->text(new RichText('Potřebujete adresu upravit? ', TextRun::link('Napište nám', 'https://example.com/contact'), ' ještě před předáním zásilky dopravci.'))
    ->spacer(8)
    ->heading('Co jsme pro vás vybrali', level: 2);

foreach ($products as $product) {
    $document = $document->add(new Group(
        children: [new Columns(
            first: [new Image(
                url: $product['imageUrl'],
                alt: $product['name'] . ' — ' . $product['variant'],
                width: $product['imageWidth'],
                height: $product['imageWidth'],
            )],
            second: [
                new Heading($product['name'], level: 3),
                new Text($product['variant']),
                new Text($product['description']),
                new Text(new RichText(
                    TextRun::strong($product['quantity'] . ' ks'),
                    ' × ' . $money($product['unitPrice']),
                    "\n",
                    TextRun::strong('Celkem ' . $money($product['quantity'] * $product['unitPrice'])),
                )),
            ],
            gap: 24,
        )],
        padding: 16,
        borderColor: $border,
    ));
}

$document = $document
    ->spacer(8)
    ->heading('Souhrn objednávky', level: 2)
    ->table(
        headers: ['Položka', 'Částka'],
        rows: [
            ['Zboží · 3 kusy', $money($subtotal)],
            ['Sleva VITEJTE', '−' . $money($discount)],
            ['Doručení na adresu', $money($shipping)],
            ['Základ DPH', $money($taxBase)],
            ['DPH 21 %', $money($vat)],
            [new RichText(TextRun::strong('Celkem včetně DPH')), new RichText(TextRun::strong($money($total)))],
        ],
    )
    ->text('Základ DPH a DPH jsou rozpisem celkové ceny, nepřičítají se znovu. Celá částka je již uhrazená.')
    ->heading('Platba a doručení', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Objednáno', '16. 11. 2026, 10:42'),
        new KeyValue('Platba', new RichText(TextRun::strong('Kartou online'), "\nUhrazeno · " . $money($total))),
        new KeyValue('Doprava', 'Kurýr na adresu'),
        new KeyValue('Očekávané doručení', '19.–20. 11. 2026'),
        new KeyValue('Stav zásilky', new RichText(TextRun::link('Sledovat v detailu', $orderUrl . '?tab=delivery&source=email'))),
    ))
    ->spacer(12)
    ->add(new Group(
        children: [
            new Heading('Co bude následovat', level: 2),
            new BulletList([
                'Pečlivě zkontrolujeme a bezpečně zabalíme všechny tři kusy.',
                'Po předání dopravci dostanete samostatný e-mail s odkazem na sledování.',
                'Doklad o nákupu najdete v detailu objednávky, kde zůstane k dispozici i později.',
            ], ordered: true),
            new Group(
                children: [new Text(new RichText(
                    TextRun::strong('Vše v jednom balíčku.'),
                    ' Křehkou keramiku oddělujeme papírovou výplní. Pokud by přesto něco nedorazilo v pořádku, ',
                    TextRun::link('ozvěte se našemu týmu', 'https://example.com/contact?topic=delivery'),
                    ' a pomůžeme vám najít řešení.',
                ))],
                backgroundColor: new Color('ffffff'),
                padding: 12,
            ),
        ],
        backgroundColor: new Color('f0fdf4'),
        padding: 16,
        borderColor: new Color('bbf7d0'),
    ))
    ->add(new Quote(
        content: new RichText(TextRun::emphasis('Prosím zazvoňte na ateliér. Pokud nebudu u dveří, chvilku počkejte — pracuji v zadní místnosti. Děkuji!')),
        attribution: 'Vaše poznámka k doručení',
    ))
    ->add(new OrderCareNote())
    ->divider($border)
    ->heading('Jsme tu pro vás', level: 2)
    ->text('Máte otázku k objednávce, materiálu nebo péči? Napište nám číslo objednávky a stručně popište, s čím vám můžeme pomoci.')
    ->button('Kontaktovat podporu', 'https://example.com/contact?order=' . $orderNumber)
    ->add(new Signature(new Contact(
        name: 'Anna Novotná',
        position: 'Zákaznická péče',
        organization: 'Dobrý domov',
        email: 'podpora@example.com',
        phone: '+420 555 010 204',
    )))
    ->text(new RichText(
        TextRun::link('Detail objednávky', $orderUrl),
        ' · ',
        TextRun::link('Péče o výrobky', 'https://example.com/care'),
        ' · ',
        TextRun::link('Vrácení zboží', 'https://example.com/returns'),
    ))
    ->spacer(24)
    ->divider($border)
    ->add(new Group(
        children: [
            new AddressBlock(new Address(
                name: 'Dobrý domov, s. r. o.',
                street: 'U Zahrady 12',
                city: 'Brno',
                postalCode: '602 00',
                country: 'Česká republika',
                email: 'podpora@example.com',
                phone: '+420 555 010 204',
            )),
            new Text('Zákaznická péče: Po–Pá, 9:00–16:00'),
            new Text(new RichText(
                'Tento e-mail se týká objednávky ' . $orderNumber . '.',
                "\n",
                TextRun::link('Obchodní podmínky', 'https://example.com/terms'),
                ' · ',
                TextRun::link('Ochrana soukromí', 'https://example.com/privacy'),
            )),
            new Text('© 2026 Dobrý domov. Fiktivní obchod a ukázková objednávka; nejde o skutečný nákup ani daňový doklad.'),
        ],
        backgroundColor: $softBackground,
        padding: 16,
    ))
    ->spacer(32)
    ->divider(new Color('cbd5e1'), thickness: 2)
    ->heading('Demonstrační příloha', level: 2)
    ->text('Následující sekce slouží pouze ke kontrole vzhledu komponent. Uvedené stavy se netýkají objednávky výše.')
    ->heading('Stavy v hlubším vnoření', level: 3)
    ->add(new Columns(
        first: [new Group(
            children: [new Columns(
                first: [new Badge('Přijato', BadgeVariant::Neutral), new Text('Nový požadavek')],
                second: [new Badge('Informace', BadgeVariant::Info), new Text('Zpráva pro zákazníka')],
                gap: 12,
            )],
            backgroundColor: $softBackground,
            padding: 12,
        )],
        second: [new Group(
            children: [new Columns(
                first: [new Badge('Pozor', BadgeVariant::Warning), new Text('Vyžaduje kontrolu')],
                second: [new Badge('Chyba', BadgeVariant::Danger), new Text('Je třeba zasáhnout')],
                gap: 12,
            )],
            backgroundColor: $softBackground,
            padding: 12,
        )],
        gap: 16,
    ))
    ->add(new Badge('Dokončeno', BadgeVariant::Success))
    ->heading('Typografie a dlouhý obsah', level: 3)
    ->heading('Doplňující informace', level: 4)
    ->text('Žluťoučký kůň úpěl ďábelské ódy. Česká diakritika, „uvozovky“, pomlčky — i symboly ×, %, &, © a € mají zůstat čitelné při zalamování na úzkém displeji.')
    ->heading('Formátovaný odkaz', level: 5)
    ->text(new RichText(
        TextRun::link('Důležitá informace & další podrobnosti', 'https://example.com/help?topic=care&lang=cs', TextStyle::StrongEmphasis),
        "\n",
        TextRun::strong('Tučně'),
        ' · ',
        TextRun::emphasis('Kurzívou'),
        ' · ',
        new TextRun('Obojí současně', TextStyle::StrongEmphasis),
    ))
    ->heading('Doslovný text, nikoli HTML', level: 6)
    ->text('Poznámka „<doručit za branku>“ & označení A/B se zobrazí doslova. Dlouhý název varianty: přírodní šalvějová s jemnou strukturou a ručně dokončeným okrajem.')
    ->add(new KeyValue('Referenční kód', 'DD-2026-10482-REKLAMACE-PECE-O-VYROBEK'))
    ->spacer(8);

return $document->render();
