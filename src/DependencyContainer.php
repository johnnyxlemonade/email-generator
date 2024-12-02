<?php

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\Services\AddressService;
use Psr\Log\LoggerInterface;
use Lemonade\EmailGenerator\EmailContext;
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

class DependencyContainer
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
     * @param EmailContext $context Kontext definující potřeby emailu.
     */
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly Translator $translator,
        protected readonly TemplateRenderer $templateRenderer,
        protected readonly BlockManager $blockManager,
        protected EmailContext $context
    ) {
        $this->initializeRequiredServices();
    }

    /**
     * Inicializace povinných služeb na základě kontextu emailu.
     *
     * @return void
     */
    private function initializeRequiredServices(): void
    {
        if ($this->context->includeProducts) {
            $this->productCollectionService = new ProductCollectionService(new ProductFactory());
        }

        if ($this->context->includeShipping) {
            $this->shippingService = new ShippingService();
        }

        if ($this->context->includePayment) {
            $this->paymentService = new PaymentService();
        }

        if ($this->context->includePickupPoint) {
            $this->pickupPointService = new PickupPointService();
        }

        if ($this->context->includeAttachments) {
            $this->attachmentCollectionService = new AttachmentCollectionService(new AttachmentFactory());
        }

        if ($this->context->includeSummary) {
            $this->summaryService = new SummaryService();
        }

        // ContextService je obecně volitelný, ale můžeme ho inicializovat vždy, pokud je třeba.
        $this->contextService = new ContextService();

        // AddressService je obecně volitelný, ale můžeme ho inicializovat vždy, pokud je třeba.
        $this->addressService = new AddressService();
    }

    /**
     *
     * @return ContextService
     */
    public function getContextService(): ContextService
    {
        return $this->getService('contextService', ContextService::class);
    }

    /**
     * @return AddressService
     */
    public function getAddressService(): AddressService
    {

        return $this->getService('addressService', AddressService::class);
    }

    /**
     * Obecná metoda pro lazy-loading služeb.
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
     * Vrací ProductCollectionService. Pokud služba nebyla inicializována, pokusí se ji vytvořit.
     *
     * @return ProductCollectionService
     * @throws \RuntimeException
     */
    public function getProductCollectionService(): ProductCollectionService
    {
        if ($this->productCollectionService === null && $this->context->includeProducts) {
            $this->logger->info('ProductCollectionService není nastavena, vytváří se nová instance.');
            $this->productCollectionService = new ProductCollectionService(new ProductFactory());
        } elseif ($this->productCollectionService === null) {
            throw new \RuntimeException('ProductCollectionService není dostupná pro tento typ emailu.');
        }
        return $this->productCollectionService;
    }

    /**
     * Vrací ShippingService. Pokud služba nebyla inicializována, pokusí se ji vytvořit.
     *
     * @return ShippingService
     * @throws \RuntimeException
     */
    public function getShippingService(): ShippingService
    {
        if ($this->shippingService === null && $this->context->includeShipping) {
            $this->logger->info('ShippingService není nastavena, vytváří se nová instance.');
            $this->shippingService = new ShippingService();
        } elseif ($this->shippingService === null) {
            throw new \RuntimeException('ShippingService není dostupná pro tento typ emailu.');
        }
        return $this->shippingService;
    }

    /**
     * Vrací PaymentService. Pokud služba nebyla inicializována, pokusí se ji vytvořit.
     *
     * @return PaymentService
     * @throws \RuntimeException
     */
    public function getPaymentService(): PaymentService
    {
        if ($this->paymentService === null && $this->context->includePayment) {
            $this->logger->info('PaymentService není nastavena, vytváří se nová instance.');
            $this->paymentService = new PaymentService();
        } elseif ($this->paymentService === null) {
            throw new \RuntimeException('PaymentService není dostupná pro tento typ emailu.');
        }
        return $this->paymentService;
    }

    /**
     * Vrací PickupPointService. Pokud služba nebyla inicializována, pokusí se ji vytvořit.
     *
     * @return PickupPointService
     * @throws \RuntimeException
     */
    public function getPickupPointService(): PickupPointService
    {
        if ($this->pickupPointService === null && $this->context->includePickupPoint) {
            $this->logger->info('PickupPointService není nastavena, vytváří se nová instance.');
            $this->pickupPointService = new PickupPointService();
        } elseif ($this->pickupPointService === null) {
            throw new \RuntimeException('PickupPointService není dostupná pro tento typ emailu.');
        }
        return $this->pickupPointService;
    }

    /**
     * Vrací AttachmentCollectionService. Pokud služba nebyla inicializována, pokusí se ji vytvořit.
     *
     * @return AttachmentCollectionService
     * @throws \RuntimeException
     */
    public function getAttachmentCollectionService(): AttachmentCollectionService
    {
        if ($this->attachmentCollectionService === null && $this->context->includeAttachments) {
            $this->logger->info('AttachmentCollectionService není nastavena, vytváří se nová instance.');
            $this->attachmentCollectionService = new AttachmentCollectionService(new AttachmentFactory());
        } elseif ($this->attachmentCollectionService === null) {
            throw new \RuntimeException('AttachmentCollectionService není dostupná pro tento typ emailu.');
        }
        return $this->attachmentCollectionService;
    }

    /**
     * Vrací SummaryService. Pokud služba nebyla inicializována, pokusí se ji vytvořit.
     *
     * @return SummaryService
     * @throws \RuntimeException
     */
    public function getSummaryService(): SummaryService
    {
        if ($this->summaryService === null && $this->context->includeSummary) {
            $this->logger->info('SummaryService není nastavena, vytváří se nová instance.');
            $this->summaryService = new SummaryService();
        } elseif ($this->summaryService === null) {
            throw new \RuntimeException('SummaryService není dostupná pro tento typ emailu.');
        }
        return $this->summaryService;
    }

    // Další povinné služby (getter metody pro Translator, TemplateRenderer, BlockManager)

    /**
     * Vrací LoggerInterface.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Vrací Translator.
     *
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Vrací TemplateRenderer.
     *
     * @return TemplateRenderer
     */
    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->templateRenderer;
    }

    /**
     * Vrací BlockManager.
     *
     * @return BlockManager
     */
    public function getBlockManager(): BlockManager
    {
        return $this->blockManager;
    }
}
