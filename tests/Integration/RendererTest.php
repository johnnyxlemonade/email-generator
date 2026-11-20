<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Component\{AddressBlock, Badge, Columns, Group, Image, KeyValue, Signature, Steps, Text};
use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Enum\FontFamily;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\{Address, Color, Contact, ImageSource, Translation};

/**
 * Verifies email structure, escaping, image sizing and text direction
 */
final class RendererTest extends EmailTestCase
{
    public function testDocumentMetadataAndImageAttributesEscapeUntrustedText(): void
    {
        $unsafe = '<script>alert("x")</script> & \'quote\' &lt;b&gt;';
        $html = $this->emails()->create(title: $unsafe, preheader: $unsafe)
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
        $html = $this->emails()->create(locale: 'cs')->text($translation)->render();
        $xpath = $this->xpath($html);

        self::assertSame(0.0, $xpath->evaluate('count(//b | //*[@onerror])'));
        self::assertSame('<b><img src=x onerror=alert(1)></b>', $xpath->evaluate('string(//p)'));
    }

    public function testImageDimensionsRespectConfiguredWidthAndAspectRatio(): void
    {
        $emails = new EmailGenerator(new HtmlRenderer($this->translator(), new EmailConfig(width: EmailWidth::Compact)));
        $xpath = $this->xpath($emails->create()->image('https://example.com/banner.png', '', 1000, 500)->render());
        self::assertSame('416', $xpath->evaluate('string(//img/@width)'));
        self::assertSame('208', $xpath->evaluate('string(//img/@height)'));
    }

    public function testDesktopWidthFlowsThroughContainersAndWidthSensitiveComponents(): void
    {
        $emails = new EmailGenerator(new HtmlRenderer($this->translator(), new EmailConfig(width: EmailWidth::Desktop)));
        $html = $emails->create()
            ->image('https://example.com/full.png', 'Full width', 2000, 1000)
            ->add(new Columns(
                first: [new Text('First')],
                second: [new Text('Second')],
                gap: 24,
            ))
            ->add(new Group(
                children: [new Image('https://example.com/group.png', 'Grouped', 2000, 1000)],
                padding: 24,
                borderColor: new Color('#cccccc'),
            ))
            ->table(['Item', 'Amount'], [['Desktop plan', '$120.00']])
            ->add(new Signature(
                new Contact('Maya Chen', 'Customer Success'),
                new ImageSource('https://example.com/maya.png', 'Maya Chen'),
            ))
            ->render();
        $xpath = $this->xpath($html);

        self::assertStringContainsString('max-width:1200px;background-color:', $html);
        self::assertSame('1136', $xpath->evaluate('string(//img[@alt="Full width"]/@width)'));
        self::assertSame('1086', $xpath->evaluate('string(//img[@alt="Grouped"]/@width)'));
        self::assertSame('112', $xpath->evaluate('string(//img[@alt="Maya Chen"]/@width)'));
        self::assertStringContainsString('max-width:556px;', $html);
        self::assertStringContainsString('max-width:1008px;', $html);
        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]/tbody)'));
    }

    public function testConfiguredFontFamilyIsUsedByTheLayoutAndAllTextRenderers(): void
    {
        $fontFamily = FontFamily::Georgia;
        $emails = new EmailGenerator(new HtmlRenderer($this->translator(), new EmailConfig(fontFamily: $fontFamily)));
        $html = $emails->create()
            ->heading('Heading')
            ->image('https://example.com/image.png', 'Image')
            ->button('Primary', 'https://example.com/primary')
            ->button('Secondary', 'https://example.com/secondary', ButtonVariant::Secondary)
            ->table(['Column'], [['Value']])
            ->add(
                new KeyValue('Label', 'Value'),
                new AddressBlock(new Address('Name', 'Street', 'City', 'Postal code', 'Country')),
                new Badge('Ready'),
                new Steps(['Created']),
            )
            ->render();

        self::assertGreaterThan(10, substr_count($html, 'font-family:' . $fontFamily->css()));
        self::assertSame(substr_count($html, 'font-family:'), substr_count($html, 'font-family:' . $fontFamily->css()));
        self::assertStringNotContainsString('font-family:Arial,Helvetica,sans-serif', $html);
    }

    public function testSignatureKeepsDesktopImageWidthAndDefinesCompactMobileColumns(): void
    {
        $html = $this->emails()->create()->add(new Signature(
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
        $html = $this->emails()->create()->add(new Steps(['Created', 'Processing', 'Delivered'], currentStep: 2))->render();
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
        $xpath = $this->xpath($this->emails()->create()->table(['Item'], [['Plan']])->render());

        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//th[@align="left" and contains(@style, "text-align:left;")])'));
        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//td[@align="left" and contains(@style, "text-align:left;")])'));
    }

    public function testTextAndHeadingApplyAlignmentWithoutChangingTypedContent(): void
    {
        $xpath = $this->xpath($this->emails()->create(locale: 'cs')
            ->text(new RichText(TextRun::strong('Centered')), alignment: HorizontalAlignment::Center)
            ->heading(new Translation('emails.activate'), level: 2, alignment: HorizontalAlignment::Right)
            ->render());

        self::assertSame(1.0, $xpath->evaluate('count(//p[@align="center" and contains(@style, "text-align:center;")]/strong[text()="Centered"])'));
        self::assertSame(1.0, $xpath->evaluate('count(//h2[@align="right" and contains(@style, "text-align:right;") and text()="Aktivovat účet"])'));
    }

    public function testTableAppliesGlobalAlignmentToHeadersAndCells(): void
    {
        $xpath = $this->xpath($this->emails()->create()->table(
            ['Item'],
            [['Plan']],
            alignment: HorizontalAlignment::Center,
        )->render());

        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//th[@align="center" and contains(@style, "text-align:center;")])'));
        self::assertSame(1.0, $xpath->evaluate('count(//table[thead]//td[@align="center" and contains(@style, "text-align:center;")])'));
    }

    public function testTableColumnAlignmentOverridesGlobalAlignment(): void
    {
        $xpath = $this->xpath($this->emails()->create()->table(
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
        $xpath = $this->xpath($this->emails()->create(locale: 'ar_EG', direction: Direction::Rtl)->text('مرحبا')->render());
        self::assertSame('ar-EG', $xpath->evaluate('string(/html/@lang)'));
        self::assertSame('rtl', $xpath->evaluate('string(/html/@dir)'));
        self::assertSame('مرحبا', $xpath->evaluate('string(//p)'));
    }
}
