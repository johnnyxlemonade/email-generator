<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Component\{Badge, Columns, Group, KeyValue, Quote};
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;

/**
 * Exercises new primitives and compositions through the framework-provided public API
 */
final class UniversalComponentsTest extends EmailTestCase
{
    public function testPublicApiRendersEveryNewComponentInIndependentLocales(): void
    {
        $container = new Container();
        (new CliKernelFactory($container))->create(self::context())->bootstrap();
        $emails = $container->get(EmailGenerator::class);
        $translation = new Translation('emails.welcome', ['name' => '<Alex>']);
        $components = new Group([
            new Columns(
                first: [
                    new Badge(new Translation('emails.activate'), BadgeVariant::Success),
                    new KeyValue('Welcome', $translation),
                ],
                second: [
                    new Quote(new RichText(TextRun::strong($translation)), 'Team & friends'),
                    KeyValue::listing(new KeyValue('One', '1'), new KeyValue('Two', '2')),
                ],
            ),
        ]);
        $base = $emails->create(locale: 'en')->heading('Shared');
        $english = $base->add($components);
        $czech = $emails->create(locale: 'cs')->add($components);
        $html = $english->render();
        self::assertStringContainsString('class="email-columns"', $html);
        self::assertStringContainsString('class="email-badge"', $html);
        self::assertStringContainsString('Welcome, &lt;Alex&gt;!', $html);
        self::assertStringContainsString('Activate account', $html);
        self::assertStringContainsString('Team &amp; friends', $html);
        self::assertStringContainsString('Vítejte, &lt;Alex&gt;!', $czech->render());
        self::assertStringNotContainsString('email-columns', $base->render());
        self::assertSame($html, $english->render());
        self::assertStringNotContainsString('<Alex>', $html);
        self::assertSame(2.0, $this->xpath($html)->evaluate('count(//table[not(@role)])'));

        $rtl = $emails->create(locale: 'ar', direction: Direction::Rtl)->add($components)->render();
        self::assertStringContainsString('dir="rtl"', $rtl);
        self::assertStringContainsString('class="email-columns"', $rtl);
        self::assertStringNotContainsString('dir="ltr"', $rtl);
    }
}
