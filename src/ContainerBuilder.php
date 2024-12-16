<?php

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Services\AddressService;
use Lemonade\EmailGenerator\Services\AttachmentCollectionService;
use Lemonade\EmailGenerator\Services\ContextService;
use Lemonade\EmailGenerator\Services\CouponCollectionService;
use Lemonade\EmailGenerator\Services\FormItemCollectionService;
use Lemonade\EmailGenerator\Services\PaymentService;
use Lemonade\EmailGenerator\Services\PickupPointService;
use Lemonade\EmailGenerator\Services\ProductCollectionService;
use Lemonade\EmailGenerator\Services\ShippingService;
use Lemonade\EmailGenerator\Services\SummaryCollectionService;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Psr\Log\LoggerInterface;

class ContainerBuilder
{
    // Lazy-loaded service instances
    private ?FormItemCollectionService $formItemCollectionService = null;
    private ?CouponCollectionService $couponCollectionService = null;
    private ?ProductCollectionService $productCollectionService = null;
    private ?AttachmentCollectionService $attachmentCollectionService = null;
    private ?SummaryCollectionService $summaryCollectionService = null;
    private ?ShippingService $shippingService = null;
    private ?PaymentService $paymentService = null;
    private ?PickupPointService $pickupPointService = null;
    private ?ContextService $contextService = null;
    private ?AddressService $addressService = null;

    /**
     * Constructor for the Dependency Container.
     * Initializes the essential services that can be further expanded based on the EmailContext.
     *
     * @param LoggerInterface $logger Logger for tracking events.
     * @param Translator $translator Translator for message translation.
     * @param TemplateRenderer $templateRenderer Renderer for rendering templates.
     * @param BlockManager $blockManager Manager for managing email content blocks.
     * @param ServiceFactoryManager $serviceFactoryManager Manager for creating service instances.
     */
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly Translator $translator,
        protected readonly TemplateRenderer $templateRenderer,
        protected readonly BlockManager $blockManager,
        protected readonly ServiceFactoryManager $serviceFactoryManager,
    ) {}

    /**
     * General method for lazy-loading services using a factory.
     * If the service is not initialized, it creates a new instance and logs it.
     *
     * @param string $serviceProperty Name of the property holding the service instance.
     * @param callable $factory Factory function that returns a new service instance.
     * @return object
     */
    private function getOrCreateService(string $serviceProperty, callable $factory): object
    {
        if ($this->$serviceProperty === null) {
            $this->logger->debug("Created new instance of $serviceProperty.");
            $this->$serviceProperty = $factory();
        }
        return $this->$serviceProperty;
    }

    /**
     * Returns an instance of ProductCollectionService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return ProductCollectionService
     */
    public function getProductCollectionService(): ProductCollectionService
    {
        return $this->getOrCreateService(
            serviceProperty: 'productCollectionService',
            factory: fn() => $this->serviceFactoryManager->createProductCollectionService()
        );
    }

    /**
     * Returns an instance of CouponCollectionService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return CouponCollectionService
     */
    public function getCouponCollectionService(): CouponCollectionService
    {
        return $this->getOrCreateService(
            serviceProperty: 'couponCollectionService',
            factory: fn() => $this->serviceFactoryManager->createCouponCollectionService()
        );
    }

    /**
     * Returns an instance of FormItemCollectionService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return FormItemCollectionService
     */
    public function getFormItemCollectionService(): FormItemCollectionService
    {
        return $this->getOrCreateService(
            serviceProperty: 'formItemCollectionService',
            factory: fn() => $this->serviceFactoryManager->createFormItemCollectionService()
        );
    }

    /**
     * Returns an instance of AttachmentCollectionService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return AttachmentCollectionService
     */
    public function getAttachmentCollectionService(): AttachmentCollectionService
    {
        return $this->getOrCreateService(
            serviceProperty: 'attachmentCollectionService',
            factory: fn() => $this->serviceFactoryManager->createAttachmentCollectionService()
        );
    }

    /**
     * Returns an instance of ShippingService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return ShippingService
     */
    public function getShippingService(): ShippingService
    {
        return $this->getOrCreateService(
            serviceProperty: 'shippingService',
            factory: fn() => $this->serviceFactoryManager->createShippingService()
        );
    }

    /**
     * Returns an instance of PaymentService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return PaymentService
     */
    public function getPaymentService(): PaymentService
    {
        return $this->getOrCreateService(
            serviceProperty: 'paymentService',
            factory: fn() => $this->serviceFactoryManager->createPaymentService()
        );
    }

    /**
     * Returns an instance of PickupPointService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return PickupPointService
     */
    public function getPickupPointService(): PickupPointService
    {
        return $this->getOrCreateService(
            serviceProperty: 'pickupPointService',
            factory: fn() => $this->serviceFactoryManager->createPickupPointService()
        );
    }

    /**
     * Returns an instance of SummaryCollectionService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return SummaryCollectionService
     */
    public function getSummaryCollectionService(): SummaryCollectionService
    {
        return $this->getOrCreateService(
            serviceProperty: 'summaryCollectionService',
            factory: fn() => $this->serviceFactoryManager->createSummaryCollectionService()
        );
    }

    // Getter methods for essential services (Logger, Translator, TemplateRenderer, BlockManager)

    /**
     * Returns an instance of LoggerInterface.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Returns an instance of Translator.
     *
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Returns an instance of TemplateRenderer.
     *
     * @return TemplateRenderer
     */
    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->templateRenderer;
    }

    /**
     * Returns an instance of BlockManager.
     *
     * @return BlockManager
     */
    public function getBlockManager(): BlockManager
    {
        return $this->blockManager;
    }

    /**
     * Returns an instance of ServiceFactoryManager.
     *
     * @return ServiceFactoryManager
     */
    public function getServiceFactoryManager(): ServiceFactoryManager
    {
        return $this->serviceFactoryManager;
    }

    // Additional services

    /**
     * Returns an instance of ContextService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return ContextService
     */
    public function getContextService(): ContextService
    {
        return $this->getOrCreateService(
            serviceProperty: 'contextService',
            factory: fn() => $this->serviceFactoryManager->createContextService()
        );
    }

    /**
     * Returns an instance of AddressService.
     * If the service has not been initialized, it attempts to create and log it.
     *
     * @return AddressService
     */
    public function getAddressService(): AddressService
    {
        return $this->getOrCreateService(
            serviceProperty: 'addressService',
            factory: fn() => $this->serviceFactoryManager->createAddressService()
        );
    }

}
