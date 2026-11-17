<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\{Contact, ImageSource, Translation};

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

    public function testSignatureKeepsDesktopImageWidthAndDefinesCompactMobileColumns(): void
    {
        $html = $this->emails->create()->add(new Signature(
            new Contact('Maya Chen', 'Customer Success'),
            new ImageSource('https://example.com/maya.png', 'Maya Chen'),
        ))->render();
        $xpath = $this->xpath($html);

        self::assertSame('112', $xpath->evaluate('string(//img/@width)'));
        self::assertStringContainsString('email-columns-mobile-first-72', $html);
        self::assertStringContainsString('.email-column-first img{width:72px!important;', $html);
    }

    public function testStepsExposeTextStatesAndACompactMobileIndicator(): void
    {
        $html = $this->emails->create()->add(new Steps(['Created', 'Processing', 'Delivered'], currentStep: 2))->render();
        $xpath = $this->xpath($html);

        self::assertSame(1.0, $xpath->evaluate('count(//*[@aria-current="step"])'));
        self::assertStringContainsString('email-step-completed', $html);
        self::assertStringContainsString('email-step-current', $html);
        self::assertStringContainsString('email-step-pending', $html);
        self::assertStringContainsString('border-left:4px solid', $html);
        self::assertStringNotContainsString('text-decoration:line-through', $html);
    }

    public function testTableUsesLeftAlignmentByDefault(): void
    {
        $xpath = $this->xpath($this->emails->create()->table(['Item'], [['Plan']])->render());

        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//th[@align="left" and contains(@style, "text-align:left;")])'));
        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//td[@align="left" and contains(@style, "text-align:left;")])'));
    }

    public function testTextAndHeadingApplyAlignmentWithoutChangingTypedContent(): void
    {
        $xpath = $this->xpath($this->emails->create(locale: 'cs')
            ->text(new RichText(TextRun::strong('Centered')), alignment: HorizontalAlignment::Center)
            ->heading(new Translation('emails.activate'), level: 2, alignment: HorizontalAlignment::Right)
            ->render());

        self::assertSame(1.0, $xpath->evaluate('count(//p[@align="center" and contains(@style, "text-align:center;")]/strong[text()="Centered"])'));
        self::assertSame(1.0, $xpath->evaluate('count(//h2[@align="right" and contains(@style, "text-align:right;") and text()="Aktivovat účet"])'));
    }

    public function testTableAppliesGlobalAlignmentToHeadersAndCells(): void
    {
        $xpath = $this->xpath($this->emails->create()->table(
            ['Item'],
            [['Plan']],
            alignment: HorizontalAlignment::Center,
        )->render());

        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//th[@align="center" and contains(@style, "text-align:center;")])'));
        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//td[@align="center" and contains(@style, "text-align:center;")])'));
    }

    public function testTableColumnAlignmentOverridesGlobalAlignment(): void
    {
        $xpath = $this->xpath($this->emails->create()->table(
            ['Item', 'Quantity', 'Amount'],
            [['Plan', '3', '$120.00'], ['Support', '1', '$25.00']],
            alignment: HorizontalAlignment::Center,
            columnAlignments: [0 => HorizontalAlignment::Left, 2 => HorizontalAlignment::Right],
        )->render());

        self::assertSame(3.0, $xpath->evaluate('count(//table[thead]//tr/*[1][@align="left" and contains(@style, "text-align:left;")])'));
        self::assertSame(3.0, $xpath->evaluate('count(//table[thead]//tr/*[2][@align="center" and contains(@style, "text-align:center;")])'));
        self::assertSame(3.0, $xpath->evaluate('count(//table[thead]//tr/*[3][@align="right" and contains(@style, "text-align:right;")])'));
    }

    public function testRtlAndRegionalLocaleAreExplicit(): void
    {
        $xpath = $this->xpath($this->emails->create(locale: 'ar_EG', direction: Direction::Rtl)->text('مرحبا')->render());
        self::assertSame('ar-EG', $xpath->evaluate('string(/html/@lang)'));
        self::assertSame('rtl', $xpath->evaluate('string(/html/@dir)'));
        self::assertSame('مرحبا', $xpath->evaluate('string(//p)'));
    }
}
