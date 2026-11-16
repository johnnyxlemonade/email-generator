<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;
use Lemonade\Framework\Core\Context\ApplicationContext;
use Lemonade\Framework\Core\Context\DebugMode;
use Lemonade\Framework\Core\Context\Environment;
use Lemonade\Framework\Core\Context\Path;

require dirname(__DIR__) . '/vendor/autoload.php';

$outputDirectory = __DIR__ . '/output';
if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
    throw new RuntimeException('Could not create the example output directory.');
}

// This executable example boots a real CLI app. Application services simply inject EmailGenerator.
$container = new Container();
$context = new ApplicationContext(Environment::Testing, new Path(__DIR__, __DIR__ . '/public'), new DebugMode(false));
(new CliKernelFactory($container))->create($context)->bootstrap();
$emails = $container->get(EmailGenerator::class);

foreach (['cs', 'en'] as $locale) {
    $html = $emails->create(
        locale: $locale,
        title: new Translation('emails.title'),
        preheader: new Translation('emails.preview'),
    )->heading(new Translation('emails.welcome', ['name' => 'Alex']))
        ->text(new Translation('emails.body'))
        ->button(new Translation('emails.activate'), 'https://example.com/activate?token=demo&source=email')
        ->spacer()->divider()->text(new Translation('emails.footer'))->render();
    file_put_contents(__DIR__ . '/output/welcome-' . $locale . '.html', $html);
}

$html = $emails->create(locale: 'cs', title: 'Přehled komponent', preheader: 'Ukázka všech základních obsahových prvků.')
    ->heading('Lemonade — přehled komponent')
    ->text("Jeden renderer, opakovaně použitelné komponenty.\nHTML je připravené bez šablonovacího enginu.")
    ->image('https://placehold.co/1072x320/png?text=LEMONADEFRAMEWORK', 'LEMONADEFRAMEWORK', 536, 160)
    ->heading('Co je připraveno', level: 2)
    ->list(['Typované komponenty', 'Izolované dokumenty', 'Překlady frameworku'])
    ->list(['Sestavte obsah', 'Vygenerujte HTML'], ordered: true)
    ->table(['Komponenta', 'Účel'], [['Heading / Text', 'Textový obsah'], ['Image / Button', 'Obrázek a odkaz'], ['List / Table', 'Strukturovaná data']])
    ->divider(new Color('#15803d'), thickness: 2)->spacer(24)
    ->add(new Group(
        children: [
            new Heading('Složený obsah', 2),
            new Text(new RichText('Group zachovává ', TextRun::strong('vizuální celek'), '. Více najdete v ', TextRun::link('dokumentaci', 'https://example.com/docs'), '.')),
        ],
        backgroundColor: new Color('f0fdf4'),
        padding: 24,
        borderColor: new Color('15803d'),
    ))
    ->button('Otevřít aplikaci', 'https://example.com')->render();
file_put_contents(__DIR__ . '/output/components.html', $html);

// A standalone preview of the universal components, including nested layout and every badge variant.
$html = $emails->create(locale: 'en', title: 'Universal email components', preheader: 'Columns, key-value pairs, badges and quotes.')
    ->heading('Your project update')
    ->text('A reusable layout for account summaries, notifications and reports.')
    ->add(new Columns(
        first: [
            new Heading('Account', level: 2),
            new Badge('Active', BadgeVariant::Success),
            KeyValue::listing(
                new KeyValue('Name', 'Alex & Sam'),
                new KeyValue('Plan', new RichText(TextRun::strong('Team'))),
                new KeyValue('Next step', new Translation('emails.activate')),
            ),
        ],
        second: [
            new Heading('Latest note', level: 2),
            new Quote(
                content: new RichText('Small improvements make a ', TextRun::strong('lasting difference'), '.'),
                attribution: 'The Lemonade team',
            ),
            new KeyValue('Documentation', new RichText(TextRun::link('Read the guide', 'https://example.com/docs'))),
        ],
        gap: 24,
    ))
    ->heading('Status variants', level: 2)
    ->add(new Columns(
        first: [new Badge('Neutral'), new Badge('Information', BadgeVariant::Info)],
        second: [new Badge('Review needed', BadgeVariant::Warning), new Badge('Action required', BadgeVariant::Danger)],
    ))
    ->heading('Nested layout', level: 2)
    ->add(new Group(
        children: [new Columns(
            first: [new Text('Columns also work inside a padded, bordered group.')],
            second: [new Quote("Your content stays readable.\nOn mobile, columns stack in order.")],
        )],
        backgroundColor: new Color('f8fafc'),
        padding: 16,
        borderColor: new Color('cbd5e1'),
    ))
    ->text('Literal input stays safe: <script> is displayed as text.')
    ->render();
file_put_contents(__DIR__ . '/output/universal-components.html', $html);

// Standalone fluent calls must retain the new immutable document.
$document = $emails->create(locale: 'ar', title: 'مرحبا', direction: Direction::Rtl);
$document = $document->heading('مرحبا بكم')->text('شكرا لتسجيلكم.');
$document = $document->button('تفعيل الحساب', 'https://example.com/activate');
$html = $document->render();
file_put_contents(__DIR__ . '/output/rtl.html', $html);

echo "Generated welcome-cs.html, welcome-en.html, components.html, universal-components.html and rtl.html in examples/output/.\n";
