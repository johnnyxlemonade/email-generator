<?php

namespace Lemonade\EmailGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\DependencyContainer;
use Lemonade\EmailGenerator\EmailContext;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Services\ContextService;
use Lemonade\EmailGenerator\Services\ProductCollectionService;
use Lemonade\EmailGenerator\Services\ShippingService;
use Lemonade\EmailGenerator\Services\PaymentService;
use Lemonade\EmailGenerator\Services\PickupPointService;
use Lemonade\EmailGenerator\Services\AttachmentCollectionService;
use Lemonade\EmailGenerator\Services\SummaryService;
use Psr\Log\LogLevel;

class DependencyContainerTest extends TestCase
{
    private DependencyContainer $container;

    protected function setUp(): void
    {
        $logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::INFO));
        $translator = new Translator($logger);
        $templateRenderer = new TemplateRenderer($logger, $translator);
        $blockManager = new BlockManager($templateRenderer, $logger, $translator);
        $context = new EmailContext(
            includeProducts: true,
            includeShipping: true,
            includePayment: true,
            includePickupPoint: true,
            includeAttachments: true,
            includeSummary: true
        );

        $this->container = new DependencyContainer($logger, $translator, $templateRenderer, $blockManager, $context);
    }

    public function testProductCollectionServiceInitialization()
    {
        $service = $this->container->getProductCollectionService();
        $this->assertInstanceOf(ProductCollectionService::class, $service);
    }

    public function testShippingServiceInitialization()
    {
        $service = $this->container->getShippingService();
        $this->assertInstanceOf(ShippingService::class, $service);
    }

    public function testPaymentServiceInitialization()
    {
        $service = $this->container->getPaymentService();
        $this->assertInstanceOf(PaymentService::class, $service);
    }

    public function testPickupPointServiceInitialization()
    {
        $service = $this->container->getPickupPointService();
        $this->assertInstanceOf(PickupPointService::class, $service);
    }

    public function testAttachmentCollectionServiceInitialization()
    {
        $service = $this->container->getAttachmentCollectionService();
        $this->assertInstanceOf(AttachmentCollectionService::class, $service);
    }

    public function testSummaryServiceInitialization()
    {
        $service = $this->container->getSummaryService();
        $this->assertInstanceOf(SummaryService::class, $service);
    }

    public function testContextServiceInitialization()
    {
        $service = $this->container->getContextService();
        $this->assertInstanceOf(ContextService::class, $service);
    }

    public function testServiceLazyLoading()
    {
        $context = new EmailContext(
            includeProducts: false,
            includeShipping: false,
            includePayment: false,
            includePickupPoint: false,
            includeAttachments: false,
            includeSummary: false
        );

        $logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::INFO));
        $translator = new Translator($logger);
        $templateRenderer = new TemplateRenderer($logger, $translator);
        $blockManager = new BlockManager($templateRenderer, $logger, $translator);

        $container = new DependencyContainer($logger, $translator, $templateRenderer, $blockManager, $context);

        $this->expectException(\RuntimeException::class);
        $container->getProductCollectionService();
    }

    public function testAddressServiceInitialization()
    {
        $service = $this->container->getAddressService();
        $this->assertInstanceOf(\Lemonade\EmailGenerator\Services\AddressService::class, $service);
    }
}
