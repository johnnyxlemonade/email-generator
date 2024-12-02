<?php

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\Services\AddressService;
use Psr\Log\LoggerInterface;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Services\ProductCollectionService;
use Lemonade\EmailGenerator\Services\ShippingService;
use Lemonade\EmailGenerator\Services\PaymentService;
use Lemonade\EmailGenerator\Services\PickupPointService;
use Lemonade\EmailGenerator\Services\AttachmentCollectionService;
use Lemonade\EmailGenerator\Services\SummaryService;
use Lemonade\EmailGenerator\Services\ContextService;
use Lemonade\EmailGenerator\Factories\ProductFactory;
use Lemonade\EmailGenerator\Factories\AttachmentFactory;

class ContainerBuilder
{

    private ?ProductCollectionService $productCollectionService = null;
    private ?ShippingService $shippingService = null;
    private ?PaymentService $paymentService = null;
    private ?PickupPointService $pickupPointService = null;
    private ?AttachmentCollectionService $attachmentCollectionService = null;
    private ?SummaryService $summaryService = null;
    private ?ContextService $contextService = null;
    private ?AddressService $addressService = null;

    /**
     * Konstruktor třídy DependencyContainer.
     * Inicializuje základní služby, které mohou být dále doplněny na základě EmailContext.
     *
     * @param LoggerInterface $logger Logger pro sledování událostí.
     * @param Translator $translator Translator pro překlad zpráv.
     * @param TemplateRenderer $templateRenderer Renderer pro vykreslování šablon.
     * @param BlockManager $blockManager Správce bloků obsahu emailu.
     */
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly Translator $translator,
        protected readonly TemplateRenderer $templateRenderer,
        protected readonly BlockManager $blockManager
    ) {}

    /**
     * Obecná metoda pro lazy-loading služeb.
     * Pokud služba není inicializována, vytvoří se nová instance a zaloguje se.
     *
     * @param string $serviceProperty Název vlastnosti, která drží instanci služby.
     * @param string $serviceClass Název třídy služby.
     * @return object
     */
    private function getService(string $serviceProperty, string $serviceClass): object
    {
        if ($this->$serviceProperty === null) {
            $this->logger->info("$serviceClass není nastavena, vytváří se nová instance.");
            $this->$serviceProperty = new $serviceClass();
        }
        return $this->$serviceProperty;
    }

    /**
     * Vrací instanci ProductCollectionService.
     * Pokud služba nebyla inicializována, pokusí se ji vytvořit a zalogovat.
     *
     * @return ProductCollectionService
     */
    public function getProductCollectionService(): ProductCollectionService
    {
        if ($this->productCollectionService === null) {
            $this->logger->warning('ProductCollectionService není nastavena, vytváří se nová instance.');
            $this->productCollectionService = new ProductCollectionService(new ProductFactory());
        }
        return $this->productCollectionService ?? $this->createAndLogService(ProductCollectionService::class, new ProductFactory());
    }

    /**
     * Vrací instanci ShippingService.
     * Pokud služba nebyla inicializována, pokusí se ji vytvořit a zalogovat.
     *
     * @return ShippingService
     */
    public function getShippingService(): ShippingService
    {
        if ($this->shippingService === null) {
            $this->logger->warning('ShippingService není nastavena, vytváří se nová instance.');
            $this->shippingService = new ShippingService();
        }
        return $this->shippingService ?? $this->createAndLogService(ShippingService::class);
    }

    /**
     * Vrací instanci PaymentService.
     * Pokud služba nebyla inicializována, pokusí se ji vytvořit a zalogovat.
     *
     * @return PaymentService
     */
    public function getPaymentService(): PaymentService
    {
        if ($this->paymentService === null) {
            $this->logger->warning('PaymentService není nastavena, vytváří se nová instance.');
            $this->paymentService = new PaymentService();
        }
        return $this->paymentService ?? $this->createAndLogService(PaymentService::class);
    }

    /**
     * Vrací instanci PickupPointService.
     * Pokud služba nebyla inicializována, pokusí se ji vytvořit a zalogovat.
     *
     * @return PickupPointService
     */
    public function getPickupPointService(): PickupPointService
    {
        if ($this->pickupPointService === null) {
            $this->logger->warning('PickupPointService není nastavena, vytváří se nová instance.');
            $this->pickupPointService = new PickupPointService();
        }
        return $this->pickupPointService ?? $this->createAndLogService(PickupPointService::class);
    }

    /**
     * Vrací instanci AttachmentCollectionService.
     * Pokud služba nebyla inicializována, pokusí se ji vytvořit a zalogovat.
     *
     * @return AttachmentCollectionService
     */
    public function getAttachmentCollectionService(): AttachmentCollectionService
    {
        if ($this->attachmentCollectionService === null) {
            $this->logger->warning('AttachmentCollectionService není nastavena, vytváří se nová instance.');
            $this->attachmentCollectionService = new AttachmentCollectionService(new AttachmentFactory());
        }
        return $this->attachmentCollectionService ?? $this->createAndLogService(AttachmentCollectionService::class, new AttachmentFactory());
    }

    /**
     * Vrací instanci SummaryService.
     * Pokud služba nebyla inicializována, pokusí se ji vytvořit a zalogovat.
     *
     * @return SummaryService
     */
    public function getSummaryService(): SummaryService
    {
        if ($this->summaryService === null) {
            $this->logger->warning('SummaryService není nastavena, vytváří se nová instance.');
            $this->summaryService = new SummaryService();
        }
        return $this->summaryService ?? $this->createAndLogService( SummaryService::class);
    }

    /**
     * Vytvoří novou instanci služby a zaloguje varování.
     *
     * @param string $serviceClass Název třídy služby.
     * @param mixed ...$args Argumenty pro konstruktor služby.
     * @return object
     */
    private function createAndLogService(string $serviceClass, ...$args)
    {
        $this->logger->critical("$serviceClass nebyla nastavena, vytváří se nová instance na vyžádání.");
        return new $serviceClass(...$args);
    }

    // Getter metody pro povinné služby (Logger, Translator, TemplateRenderer, BlockManager)

    /**
     * Vrací instanci LoggerInterface.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Vrací instanci Translator.
     *
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Vrací instanci TemplateRenderer.
     *
     * @return TemplateRenderer
     */
    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->templateRenderer;
    }

    /**
     * Vrací instanci BlockManager.
     *
     * @return BlockManager
     */
    public function getBlockManager(): BlockManager
    {
        return $this->blockManager;
    }

    // dalsi sluzby

    /**
     *
     * @return ContextService
     */
    public function getContextService(): ContextService
    {
        return $this->getService(serviceProperty: "contextService", serviceClass: ContextService::class);
    }

    /**
     * @return AddressService
     */
    public function getAddressService(): AddressService
    {

        return $this->getService(serviceProperty: "addressService", serviceClass: AddressService::class);
    }

}
