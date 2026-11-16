<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use TypeError;

/**
 * Verifies safe inline formatting, localization and immutable text runs
 */
final class RichTextTest extends EmailTestCase
{
    public function testMixedRunsPreserveOrderAndRenderOnlySupportedMarkup(): void
    {
        $content = new RichText(
            'Hello ',
            TextRun::strong('Alex'),
            ', ',
            TextRun::emphasis('please'),
            ' visit ',
            TextRun::link('your account', 'https://example.com/?a=1&b=2', TextStyle::StrongEmphasis),
            '.',
        );
        $xpath = $this->xpath($this->emails->create()->text($content)->render());
        self::assertSame('Hello Alex, please visit your account.', $xpath->evaluate('string(//p)'));
        self::assertSame('Alex', $xpath->evaluate('string(//p/strong)'));
        self::assertSame('please', $xpath->evaluate('string(//p/em)'));
        self::assertSame('your account', $xpath->evaluate('string(//p/a/strong/em)'));
        self::assertSame('https://example.com/?a=1&b=2', $xpath->evaluate('string(//p/a/@href)'));
        self::assertSame(0.0, $xpath->evaluate('count(//a//a)'));
    }

    public function testRichTextWorksInHeadingsListsAndTableCells(): void
    {
        $rich = new RichText('A ', TextRun::strong('value'), ' ', TextRun::link('link', 'https://example.com'));
        $xpath = $this->xpath($this->emails->create()->heading($rich)->text($rich)->list([$rich])->table([$rich], [[$rich]])->render());
        foreach (['//h1', '//p', '//li', '//th', '//tbody/tr/td'] as $selector) {
            self::assertSame('A value link', $xpath->evaluate('string(' . $selector . ')'));
            self::assertSame('value', $xpath->evaluate('string(' . $selector . '/strong)'));
            self::assertSame('https://example.com', $xpath->evaluate('string(' . $selector . '/a/@href)'));
        }
    }

    public function testEveryRunEscapesRawHtmlAndPreservesLineBreaks(): void
    {
        $unsafe = '<img src=x onerror="evil"> &lt;b&gt;';
        $content = new RichText($unsafe, "\r\n", TextRun::strong($unsafe), "\r", TextRun::emphasis($unsafe), "\n", TextRun::link($unsafe, 'https://example.com'));
        $html = $this->emails->create()->text($content)->render();
        $xpath = $this->xpath($html);
        self::assertSame(0.0, $xpath->evaluate('count(//img | //*[@onerror])'));
        self::assertSame(3.0, $xpath->evaluate('count(//p/br)'));
        self::assertSame($unsafe, $xpath->evaluate('string(//strong)'));
        self::assertSame($unsafe, $xpath->evaluate('string(//em)'));
        self::assertSame($unsafe, $xpath->evaluate('string(//p/a)'));
        self::assertStringContainsString('&amp;lt;b&amp;gt;', $html);
    }

    public function testTranslationsResolvePerDocumentAndEscapeReplacementMarkup(): void
    {
        $this->translator->setLocale('de');
        $rich = new RichText(
            TextRun::strong(new Translation('emails.welcome', ['name' => '<Alex>'])),
            ' ',
            TextRun::link(new Translation('emails.activate'), 'https://example.com'),
        );
        $cs = $this->emails->create(locale: 'cs')->group(new Text($rich));
        $en = $this->emails->create(locale: 'en')->text($rich);
        self::assertStringContainsString('Vítejte, &lt;Alex&gt;!', $cs->render());
        self::assertStringContainsString('Welcome, &lt;Alex&gt;!', $en->render());
        self::assertSame('Aktivovat účet', $this->xpath($cs->render())->evaluate('string(//p/a)'));
        self::assertSame('Activate account', $this->xpath($en->render())->evaluate('string(//p/a)'));
        self::assertSame('de', $this->translator->locale());
        $translatedHtml = new RichText(TextRun::emphasis(new Translation('emails.html', ['value' => '<script>x</script>'])));
        self::assertStringContainsString('&lt;b&gt;&lt;script&gt;x&lt;/script&gt;&lt;/b&gt;', $this->emails->create(locale: 'cs')->text($translatedHtml)->render());
    }

    public function testRichTextAndRunsAreImmutableAndReusable(): void
    {
        $rich = new RichText('Before', TextRun::strong('After'));
        self::assertTrue((new ReflectionClass($rich))->isReadOnly());
        self::assertTrue((new ReflectionClass(TextRun::class))->isReadOnly());
        $runs = $rich->runs();
        $runs[] = new TextRun('External');
        self::assertCount(2, $rich->runs());
        $first = $this->emails->create()->text($rich);
        $second = $this->emails->create()->text($rich)->text('Only second');
        self::assertStringNotContainsString('Only second', $first->render());
        self::assertStringContainsString('Only second', $second->render());
    }

    public function testEmptyRichTextMatchesEmptyPlainText(): void
    {
        self::assertSame($this->emails->create()->text('')->render(), $this->emails->create()->text(new RichText())->render());
    }

    public function testRunsCannotContainAnotherRichTextOrNestedLinks(): void
    {
        $this->expectException(TypeError::class);
        (new ReflectionClass(TextRun::class))->newInstance(new RichText(TextRun::link('Inner', 'https://example.com')));
    }

    #[DataProvider('unsafeUrls')]
    public function testInlineLinksUseTheSameUrlValidation(string $url): void
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

    public function testInvalidUtf8FailsBeforeRendering(): void
    {
        $this->expectException(InvalidInputException::class);
        new RichText("\xFF");
    }
}
