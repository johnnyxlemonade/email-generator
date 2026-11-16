<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Badge, Group, KeyValue, Quote, Table, Text};
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

/**
 * Covers immutable status labels and compositions built from existing components
 */
final class UniversalComponentTest extends EmailTestCase
{
    public function testKeyValueComposesSinglePairsAndAlignedLists(): void
    {
        $label = new Translation('emails.activate');
        $value = new RichText(TextRun::strong('<value>'), "\n", TextRun::link('Details & more', 'https://example.com/?a=1&b=2'));
        $pair = new KeyValue($label, $value);
        self::assertSame($label, $pair->label());
        self::assertSame($value, $pair->value());
        $single = $this->emails->create(locale: 'en')->add($pair);
        self::assertInstanceOf(Table::class, $single->components()[0]);
        self::assertSame($this->emails->create(locale: 'en')->table([], [[$label, $value]])->render(), $single->render());

        $secondLabel = new RichText(TextRun::emphasis('Second'));
        $second = new KeyValue($secondLabel, 'Two');
        $items = [&$second];
        $listing = KeyValue::listing($pair, ...$items);
        $second = new KeyValue('Changed', 'Changed');
        $copy = $listing->rows();
        $copy[0][0] = 'Changed through getter';
        self::assertSame([[$label, $value], [$secondLabel, 'Two']], $listing->rows());
        $html = $this->emails->create(locale: 'en')->add($listing)->render();
        self::assertStringContainsString('Activate account', $html);
        self::assertStringContainsString('<strong style="font-weight:bold;">&lt;value&gt;</strong><br>', $html);
        self::assertStringContainsString('href="https://example.com/?a=1&amp;b=2"', $html);
        self::assertStringContainsString('<em style="font-style:italic;">Second</em>', $html);
        self::assertSame(2.0, $this->xpath($html)->evaluate('count(//table[not(@role)]/tbody/tr)'));
    }

    public function testQuoteComposesTheDocumentedGroup(): void
    {
        $content = new RichText(TextRun::emphasis('<Quote>'), "\nSecond line");
        $attribution = new Translation('emails.welcome', ['name' => '<Author>']);
        $quote = new Quote($content, $attribution);
        self::assertSame($content, $quote->content());
        self::assertSame($attribution, $quote->attribution());
        $document = $this->emails->create(locale: 'en')->add($quote);
        self::assertInstanceOf(Group::class, $document->components()[0]);
        $expected = new Group(
            children: [new Text($content), new Text($attribution)],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
            borderColor: new Color('cbd5e1'),
        );
        self::assertSame($this->emails->create(locale: 'en')->add($expected)->render(), $document->render());
        self::assertStringContainsString('&lt;Quote&gt;</em><br>Second line', $document->render());
        self::assertStringContainsString('Welcome, &lt;Author&gt;!', $document->render());
        self::assertNull((new Quote('Plain'))->attribution());
        self::assertSame(1.0, $this->xpath($this->emails->create()->add(new Quote('Plain'))->render())->evaluate('count(//p)'));
    }

    public function testBadgeVariantsAndTranslations(): void
    {
        $palettes = [
            BadgeVariant::Neutral->value => ['#f1f5f9', '#334155'],
            BadgeVariant::Info->value => ['#dbeafe', '#1e40af'],
            BadgeVariant::Success->value => ['#dcfce7', '#166534'],
            BadgeVariant::Warning->value => ['#fef3c7', '#92400e'],
            BadgeVariant::Danger->value => ['#fee2e2', '#991b1b'],
        ];
        foreach (BadgeVariant::cases() as $variant) {
            $badge = new Badge('<Ready & waiting>', $variant);
            self::assertSame('<Ready & waiting>', $badge->label());
            self::assertSame($variant, $badge->variant());
            $html = $this->emails->create()->add($badge)->render();
            [$background, $foreground] = $palettes[$variant->value];
            self::assertStringContainsString('bgcolor="' . $background . '"', $html);
            self::assertStringContainsString('color:' . $foreground . ';', $html);
            self::assertStringContainsString('&lt;Ready &amp; waiting&gt;', $html);
            self::assertStringNotContainsString('<Ready', $html);
        }
        $translation = new Translation('emails.activate');
        $badge = new Badge($translation);
        self::assertSame($translation, $badge->label());
        self::assertSame(BadgeVariant::Neutral, $badge->variant());
        self::assertStringContainsString('Activate account', $this->emails->create(locale: 'en')->add($badge)->render());
    }

    public function testComponentsAreImmutable(): void
    {
        foreach ([Badge::class, KeyValue::class, Quote::class] as $class) {
            $reflection = new ReflectionClass($class);
            self::assertTrue($reflection->isFinal());
            self::assertTrue($reflection->isReadOnly());
            foreach ($reflection->getProperties() as $property) {
                self::assertTrue($property->isPrivate());
            }
        }
    }

    /**
     * @param Closure(): object $construct
     */
    #[DataProvider('invalidContent')]
    public function testInvalidContentIsRejected(Closure $construct): void
    {
        try {
            $construct();
            self::fail('Invalid text was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame(ErrorCode::TextInvalid, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{Closure(): object}>
     */
    public static function invalidContent(): iterable
    {
        yield 'badge label' => [static fn(): Badge => new Badge("Bad\0")];
        yield 'pair label' => [static fn(): KeyValue => new KeyValue("Bad\0", 'Value')];
        yield 'pair value' => [static fn(): KeyValue => new KeyValue('Label', "\xff")];
        yield 'quote content' => [static fn(): Quote => new Quote("Bad\0")];
        yield 'quote attribution' => [static fn(): Quote => new Quote('Content', "Bad\0")];
    }
}
