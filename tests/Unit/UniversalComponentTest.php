<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Alert, Badge, Button, Group, Hero, Image, KeyValue, Quote, SocialLinks, Table, Text};
use Lemonade\EmailGenerator\Enum\{AlertVariant, BadgeVariant};
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\{ImageSource, SocialLink, Translation};
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies logical composition output and validation without rendering
 */
final class UniversalComponentTest extends TestCase
{
    public function testKeyValueBuildsAStableTableListing(): void
    {
        $value = new RichText(TextRun::strong('Team'));
        $first = new KeyValue('Plan', $value);
        $second = new KeyValue('Seats', '12');
        $listing = KeyValue::listing($first, $second);
        $rows = $listing->rows();
        $rows[0][0] = 'Changed';

        self::assertSame([['Plan', $value], ['Seats', '12']], $listing->rows());
        self::assertTrue($listing->compact());
        self::assertInstanceOf(Table::class, iterator_to_array($first->components())[0]);
    }

    public function testQuoteComposesOnlyPresentContent(): void
    {
        $content = new RichText(TextRun::emphasis('Quoted'));
        $withAttribution = iterator_to_array((new Quote($content, 'Author'))->components())[0];
        $withoutAttribution = iterator_to_array((new Quote($content))->components())[0];

        self::assertInstanceOf(Group::class, $withAttribution);
        self::assertCount(2, $withAttribution->children());
        self::assertContainsOnlyInstancesOf(Text::class, $withAttribution->children());
        self::assertCount(1, $withoutAttribution->children());
    }

    public function testBadgeKeepsTypedVariantAndTranslation(): void
    {
        $label = new Translation('emails.activate');
        $badge = new Badge($label, BadgeVariant::Success);

        self::assertSame($label, $badge->label());
        self::assertSame(BadgeVariant::Success, $badge->variant());
    }

    public function testHeroComposesOptionalMediaAndAction(): void
    {
        $heading = new Translation('emails.hero_heading');
        $image = new ImageSource('https://example.com/hero.png', 'Product preview');
        $button = new Button('Explore', 'https://example.com/product');
        $hero = new Hero($heading, 'A focused introduction.', $image, $button);
        $group = iterator_to_array($hero->components())[0];

        self::assertSame($heading, $hero->heading());
        self::assertSame($image, $hero->image());
        self::assertSame($button, $hero->button());
        self::assertInstanceOf(Group::class, $group);
        self::assertContainsOnlyInstancesOf(Image::class, [$group->children()[0]]);
        self::assertSame($button, $group->children()[3]);
        self::assertCount(2, iterator_to_array((new Hero('Heading', 'Description'))->components())[0]->children());
    }

    public function testAlertComposesOnlyPresentContentWithVariantColors(): void
    {
        $alert = new Alert('Deployment failed.', 'Action required', AlertVariant::Error);
        $group = iterator_to_array($alert->components())[0];
        $withoutHeading = iterator_to_array((new Alert(new Translation('emails.alert')))->components())[0];

        self::assertSame(AlertVariant::Error, $alert->variant());
        self::assertSame('#fef2f2', $group->backgroundColor()?->hex());
        self::assertSame('#fca5a5', $group->borderColor()?->hex());
        self::assertCount(2, $group->children());
        self::assertCount(1, $withoutHeading->children());
    }

    public function testSocialLinksComposeArbitraryTextLinks(): void
    {
        $links = new SocialLinks([
            new SocialLink('Community', 'https://community.example.com/northstar'),
            new SocialLink(new Translation('emails.social_updates'), 'https://updates.example.com/northstar'),
        ]);
        $components = iterator_to_array($links->components());

        self::assertCount(2, $links->links());
        self::assertContainsOnlyInstancesOf(Text::class, $components);
        self::assertSame([], iterator_to_array((new SocialLinks([]))->components()));
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
        yield 'hero heading' => [static fn(): Hero => new Hero("Bad\0", 'Description')];
        yield 'hero description' => [static fn(): Hero => new Hero('Heading', "Bad\0")];
        yield 'alert content' => [static fn(): Alert => new Alert("Bad\0")];
        yield 'alert heading' => [static fn(): Alert => new Alert('Content', "Bad\0")];
        yield 'social label' => [static fn(): SocialLink => new SocialLink("Bad\0", 'https://example.com')];
    }
}
