<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use DOMElement;
use Lemonade\EmailGenerator\Component\{Button, Heading, Text};
use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Verifies email structure, escaping, image sizing and text direction
 */
final class RendererTest extends EmailTestCase
{
    public function testAllComponentsProduceStructuredEmailHtml(): void
    {
        $html = $this->emails->create(locale: 'cs', title: 'Ukázka', preheader: 'Preview')
            ->heading('Title', 2)->text("First\nSecond")
            ->image('https://example.com/image.png', 'Description', 120, 60)
            ->button('Activate', 'https://example.com/activate?a=1&b=2')
            ->list(['One', 'Two'])->list(['First', 'Second'], ordered: true)
            ->table(['Name', 'Amount'], [['Tea', '10 Kč'], ['Coffee', '20 Kč']])
            ->divider()->spacer(24)->group(new Text('Grouped'))->render();
        $xpath = $this->xpath($html);
        self::assertSame('Title', $xpath->evaluate('string(//h2)'));
        self::assertSame(1.0, $xpath->evaluate('count(//p/br)'));
        self::assertSame(2.0, $xpath->evaluate('count(//ul/li)'));
        self::assertSame(2.0, $xpath->evaluate('count(//ol/li)'));
        self::assertSame(2.0, $xpath->evaluate('count(//th[@scope="col"])'));
        self::assertSame(1.0, $xpath->evaluate('count(//table[not(@role)])'));
        self::assertSame('https://example.com/activate?a=1&b=2', $xpath->evaluate('string(//a/@href)'));
        self::assertSame('Description', $xpath->evaluate('string(//img/@alt)'));
        self::assertSame('120', $xpath->evaluate('string(//img/@width)'));
        self::assertSame('60', $xpath->evaluate('string(//img/@height)'));
        self::assertStringContainsString('mso-line-height-rule:exactly', $html);
        self::assertStringContainsString('<!--[if mso]>', $html);
        self::assertStringContainsString('<o:PixelsPerInch>96</o:PixelsPerInch>', $html);
        self::assertStringContainsString('max-width:600px', $html);
        self::assertStringContainsString('mso-hide:all', $html);
        self::assertStringNotContainsString('<script', $html);
        self::assertStringNotContainsString('<link', $html);
        self::assertStringNotContainsString('display:flex', $html);
        $tables = $xpath->query('//table');
        self::assertNotFalse($tables);
        foreach ($tables as $table) {
            self::assertInstanceOf(DOMElement::class, $table);
            self::assertSame('0', $table->getAttribute('cellspacing'));
            self::assertSame('0', $table->getAttribute('cellpadding'));
        }
    }

    public function testEveryDynamicTextPositionIsEscapedIncludingAttributesAndMetadata(): void
    {
        $unsafe = '<script>alert("x")</script> & \'quote\' &lt;b&gt;';
        $html = $this->emails->create(title: $unsafe, preheader: $unsafe)
            ->heading($unsafe)->text($unsafe)->button($unsafe, 'https://example.com/?a=1&b=2')
            ->image('https://example.com/a.png?x=1&y=2', $unsafe)
            ->list([$unsafe])->table([$unsafe], [[$unsafe]])->render();
        $xpath = $this->xpath($html);
        self::assertSame(0.0, $xpath->evaluate('count(//script)'));
        foreach (['//title', '//h1', '//p', '//a', '//li', '//th', '//tbody/tr/td', '//img/@alt', '//div[@aria-hidden="true"]'] as $selector) {
            self::assertSame($unsafe, $xpath->evaluate('string(' . $selector . ')'), $selector);
        }
        self::assertStringContainsString('&lt;script&gt;alert(&quot;x&quot;)', $html);
        self::assertStringContainsString('&amp;lt;b&amp;gt;', $html);
        self::assertStringNotContainsString('<script>', $html);
    }

    public function testTranslationsAndReplacementsAreEscapedAfterTranslation(): void
    {
        $translation = new Translation('emails.html', ['value' => '<img src=x onerror=alert(1)>']);
        $html = $this->emails->create(locale: 'cs', title: $translation, preheader: $translation)
            ->heading($translation)->text($translation)->button($translation, 'https://example.com')
            ->image('https://example.com/a.png', $translation)->list([$translation])
            ->table([$translation], [[$translation]])->render();
        $xpath = $this->xpath($html);
        self::assertSame(0.0, $xpath->evaluate('count(//b | //*[@onerror])'));
        self::assertSame('<b><img src=x onerror=alert(1)></b>', $xpath->evaluate('string(//h1)'));
        self::assertSame(1.0, $xpath->evaluate('count(//img)'));
        self::assertStringContainsString('&lt;b&gt;&lt;img src=x onerror=alert(1)&gt;&lt;/b&gt;', $html);
    }

    public function testImageDimensionsRespectConfiguredWidthAndAspectRatio(): void
    {
        $emails = new EmailGenerator(new HtmlRenderer($this->translator, new EmailConfig(width: EmailWidth::Compact)));
        $xpath = $this->xpath($emails->create()->image('https://example.com/banner.png', '', 1000, 500)->render());
        self::assertSame('416', $xpath->evaluate('string(//img/@width)'));
        self::assertSame('208', $xpath->evaluate('string(//img/@height)'));
        self::assertStringContainsString('height:auto', (string) $xpath->evaluate('string(//img/@style)'));
    }

    public function testFluentAndDirectCompositionHaveIdenticalRendering(): void
    {
        $fluent = $this->emails->create()->heading('Title')->text('Body')->button('Go', 'https://example.com');
        $direct = $this->emails->create()->add(new Heading('Title'), new Text('Body'), new Button('Go', 'https://example.com'));
        self::assertSame($fluent->render(), $direct->render());
    }

    public function testRtlAndRegionalLocaleAreExplicit(): void
    {
        $xpath = $this->xpath($this->emails->create(locale: 'ar_EG', direction: Direction::Rtl)->text('مرحبا')->render());
        self::assertSame('ar-EG', $xpath->evaluate('string(/html/@lang)'));
        self::assertSame('rtl', $xpath->evaluate('string(/html/@dir)'));
        self::assertSame('مرحبا', $xpath->evaluate('string(//p)'));
    }

    public function testLineEndingsAreNormalizedAfterEscaping(): void
    {
        $html = $this->emails->create()->text("A\r\nB\rC\n<D>")->render();
        self::assertStringContainsString('A<br>B<br>C<br>&lt;D&gt;', $html);
    }

    public function testHeaderlessTableAndEmptyListRender(): void
    {
        $html = $this->emails->create()->list([])->table([], [['One', 'Two']])->render();
        $xpath = $this->xpath($html);
        self::assertSame(0.0, $xpath->evaluate('count(//ul | //th)'));
        self::assertSame(2.0, $xpath->evaluate('count(//tbody/tr/td)'));
    }
}
