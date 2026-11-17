<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

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
    public function testDocumentMetadataAndImageAttributesEscapeUntrustedText(): void
    {
        $unsafe = '<script>alert("x")</script> & \'quote\' &lt;b&gt;';
        $html = $this->emails->create(title: $unsafe, preheader: $unsafe)
            ->image('https://example.com/a.png', $unsafe)
            ->render();
        $xpath = $this->xpath($html);

        self::assertSame(0.0, $xpath->evaluate('count(//script)'));
        self::assertSame($unsafe, $xpath->evaluate('string(//title)'));
        self::assertSame($unsafe, $xpath->evaluate('string(//img/@alt)'));
        self::assertSame($unsafe, $xpath->evaluate('string(//div[@aria-hidden="true"])'));
    }

    public function testTranslatedMarkupIsEscapedAfterTranslation(): void
    {
        $translation = new Translation('emails.html', ['value' => '<img src=x onerror=alert(1)>']);
        $html = $this->emails->create(locale: 'cs')->text($translation)->render();
        $xpath = $this->xpath($html);

        self::assertSame(0.0, $xpath->evaluate('count(//b | //*[@onerror])'));
        self::assertSame('<b><img src=x onerror=alert(1)></b>', $xpath->evaluate('string(//p)'));
    }

    public function testImageDimensionsRespectConfiguredWidthAndAspectRatio(): void
    {
        $emails = new EmailGenerator(new HtmlRenderer($this->translator, new EmailConfig(width: EmailWidth::Compact)));
        $xpath = $this->xpath($emails->create()->image('https://example.com/banner.png', '', 1000, 500)->render());
        self::assertSame('416', $xpath->evaluate('string(//img/@width)'));
        self::assertSame('208', $xpath->evaluate('string(//img/@height)'));
    }

    public function testRtlAndRegionalLocaleAreExplicit(): void
    {
        $xpath = $this->xpath($this->emails->create(locale: 'ar_EG', direction: Direction::Rtl)->text('مرحبا')->render());
        self::assertSame('ar-EG', $xpath->evaluate('string(/html/@lang)'));
        self::assertSame('rtl', $xpath->evaluate('string(/html/@dir)'));
        self::assertSame('مرحبا', $xpath->evaluate('string(//p)'));
    }
}
