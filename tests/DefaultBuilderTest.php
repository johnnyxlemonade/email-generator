<?php

namespace Tests\Unit\EmailGenerator;

namespace Tests\Unit\EmailGenerator;

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DefaultBuilder;
use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Collection\CouponCollection;
use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\Models\Address;
use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\Models\PickupPoint;
use Lemonade\EmailGenerator\Models\Shipping;
use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use PHPUnit\Framework\TestCase;

class DefaultBuilderTest extends TestCase
{
    private DefaultBuilder $builder;
    private $containerBuilderMock;
    private $blockManagerMock;

    protected function setUp(): void
    {
        // Mock ContainerBuilder
        $this->containerBuilderMock = $this->createMock(ContainerBuilder::class);

        // Mock BlockManager
        $this->blockManagerMock = $this->createMock(BlockManager::class);

        // Set up the mocks
        $this->containerBuilderMock
            ->method('getBlockManager')
            ->willReturn($this->blockManagerMock);

        $this->builder = new DefaultBuilder(
            container: $this->containerBuilderMock,
            language: SupportedLanguage::LANG_CS, // Nastavení výchozího jazyka
            currency: SupportedCurrencies::EUR   // Nastavení měny
        );
    }

    public function testConstructorWithCenterTrue(): void
    {
        $this->blockManagerMock
            ->expects($this->once())
            ->method('setBlockRenderCenter');

        $this->blockManagerMock
            ->expects($this->once())
            ->method('setCurrency')
            ->with(SupportedCurrencies::EUR);

        new DefaultBuilder($this->containerBuilderMock, currency: SupportedCurrencies::EUR, center: true);
    }

    public function testConstructorWithCenterFalse(): void
    {
        $this->blockManagerMock
            ->expects($this->never())
            ->method('setBlockRenderCenter');

        $this->blockManagerMock
            ->expects($this->once())
            ->method('setCurrency')
            ->with(SupportedCurrencies::EUR);

        new DefaultBuilder($this->containerBuilderMock, currency: SupportedCurrencies::EUR, center: false);
    }

    public function testConstructorUsesDefaultCurrency(): void
    {
        $this->blockManagerMock
            ->expects($this->once())
            ->method('setCurrency')
            ->with(null);

        new DefaultBuilder($this->containerBuilderMock);
    }

    public function testConstructorUsesDefaultLanguage(): void
    {
        $translatorMock = $this->createMock(\Lemonade\EmailGenerator\Localization\Translator::class);
        $this->containerBuilderMock
            ->method('getTranslator')
            ->willReturn($translatorMock);

        $translatorMock
            ->expects($this->never())
            ->method('setLanguage'); // Jazyk by neměl být přepsán.

        new DefaultBuilder($this->containerBuilderMock, currency: SupportedCurrencies::EUR);
    }

    public function testConstructorOverridesLanguage(): void
    {
        $translatorMock = $this->createMock(\Lemonade\EmailGenerator\Localization\Translator::class);
        $this->containerBuilderMock
            ->method('getTranslator')
            ->willReturn($translatorMock);

        $translatorMock
            ->expects($this->once())
            ->method('setLanguage')
            ->with(SupportedLanguage::LANG_EN);

        new DefaultBuilder($this->containerBuilderMock, language: SupportedLanguage::LANG_EN);
    }

    public function testConstructorWithAllParameters(): void
    {
        $translatorMock = $this->createMock(\Lemonade\EmailGenerator\Localization\Translator::class);
        $this->containerBuilderMock
            ->method('getTranslator')
            ->willReturn($translatorMock);

        $this->blockManagerMock
            ->expects($this->once())
            ->method('setBlockRenderCenter');

        $this->blockManagerMock
            ->expects($this->once())
            ->method('setCurrency')
            ->with(SupportedCurrencies::USD);

        $translatorMock
            ->expects($this->once())
            ->method('setLanguage')
            ->with(SupportedLanguage::LANG_FR);

        new DefaultBuilder(
            $this->containerBuilderMock,
            language: SupportedLanguage::LANG_FR,
            currency: SupportedCurrencies::USD,
            center: true
        );
    }

    public function testAddOrderInfo(): void
    {
        $orderId = 12345;
        $orderCode = "ABCDE";
        $orderDate = "2024-12-15";
        $statusUrl = "http://example.com/status";

        $this->blockManagerMock
            ->expects($this->exactly(2))
            ->method('addBlock');

        $this->builder->addOrderInfo($orderId, $orderCode, $orderDate, $statusUrl);
    }

    public function testAddPickup(): void
    {
        $pickupPointMock = $this->createMock(PickupPoint::class);

        $this->blockManagerMock
            ->expects($this->once())
            ->method('addBlock');

        $this->builder->addPickup($pickupPointMock);
    }

    public function testAddCustomerMessage(): void
    {
        $message = "Hello, customer!";

        $this->blockManagerMock
            ->expects($this->once())
            ->method('addBlock');

        $this->builder->addCustomerMessage($message);
    }

    public function testAddBillingShippingAddress(): void
    {
        $billingMock = $this->createMock(Address::class);
        $shippingMock = $this->createMock(Address::class);

        $this->blockManagerMock
            ->expects($this->once())
            ->method('addBlock');

        $this->builder->addBillingShippingAddress($billingMock, $shippingMock);
    }

    public function testAddFooter(): void
    {
        $addressMock = $this->createMock(Address::class);

        $this->blockManagerMock
            ->expects($this->exactly(2))
            ->method('addBlock');

        $this->builder->addFooter($addressMock);
    }

    public function testBuild(): void
    {
        $htmlOutput = "<html>Test Email</html>";

        $this->blockManagerMock
            ->expects($this->once())
            ->method('getHtml')
            ->willReturn($htmlOutput);

        $this->assertEquals($htmlOutput, $this->builder->build());
    }
}
