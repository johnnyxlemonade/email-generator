<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies immutable rich-text runs, styles and input validation
 */
final class RichTextTest extends TestCase
{
    public function testMixedRunsPreserveOrderAndTypedFormatting(): void
    {
        $translation = new Translation('emails.activate');
        $rich = new RichText(
            'Plain ',
            TextRun::strong('bold'),
            TextRun::emphasis($translation),
            TextRun::link('account', 'https://example.com/?a=1&b=2', TextStyle::StrongEmphasis),
        );
        $runs = $rich->runs();
        $runs[] = new TextRun('External');

        self::assertCount(4, $rich->runs());
        self::assertSame(TextStyle::Plain, $rich->runs()[0]->style());
        self::assertSame(TextStyle::Strong, $rich->runs()[1]->style());
        self::assertSame($translation, $rich->runs()[2]->content());
        self::assertSame(TextStyle::StrongEmphasis, $rich->runs()[3]->style());
        self::assertSame('https://example.com/?a=1&b=2', $rich->runs()[3]->url());
    }

    public function testEmptyRichTextContainsNoRuns(): void
    {
        self::assertSame([], (new RichText())->runs());
    }

    #[DataProvider('unsafeUrls')]
    public function testInlineLinksUseSharedUrlValidation(string $url): void
    {
        try {
            TextRun::link('Click', $url);
            self::fail('Unsafe link accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame(ErrorCode::UrlInvalid, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unsafeUrls(): iterable
    {
        yield from ComponentTest::unsafeUrls();
    }

    public function testInvalidUtf8FailsAtConstruction(): void
    {
        $this->expectException(InvalidInputException::class);
        new RichText("\xFF");
    }
}
