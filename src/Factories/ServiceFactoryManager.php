<?php

namespace Lemonade\EmailGenerator\Factories;

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

/**
 * Class ServiceFactoryManager
 * Provides factory methods for creating instances of various services.
 */
class ServiceFactoryManager
{
    /**
     * Creates an instance of ProductCollectionService.
     *
     * @return ProductCollectionService A new instance of ProductCollectionService.
     */
    public function createProductCollectionService(): ProductCollectionService
    {
        return new ProductCollectionService(new ProductFactory());
    }

    /**
     * Creates an instance of CouponCollectionService.
     *
     * @return CouponCollectionService A new instance of CouponCollectionService.
     */
    public function createCouponCollectionService(): CouponCollectionService
    {
        return new CouponCollectionService(new CouponFactory());
    }

    /**
     * Creates an instance of AttachmentCollectionService.
     *
     * @return AttachmentCollectionService A new instance of AttachmentCollectionService.
     */
    public function createAttachmentCollectionService(): AttachmentCollectionService
    {
        return new AttachmentCollectionService(new AttachmentFactory());
    }

    /**
     * Creates an instance of FormItemCollectionService.
     *
     * @return FormItemCollectionService A new instance of FormItemCollectionService.
     */
    public function createFormItemCollectionService(): FormItemCollectionService
    {
        return new FormItemCollectionService(new FormItemFactory());
    }

    /**
     * Creates an instance of SummaryCollectionService.
     *
     * @return SummaryCollectionService A new instance of SummaryCollectionService.
     */
    public function createSummaryCollectionService(): SummaryCollectionService
    {
        return new SummaryCollectionService(new SummaryFactory());
    }

    /**
     * Creates an instance of ShippingService.
     *
     * @return ShippingService A new instance of ShippingService.
     */
    public function createShippingService(): ShippingService
    {
        return new ShippingService();
    }

    /**
     * Creates an instance of PaymentService.
     *
     * @return PaymentService A new instance of PaymentService.
     */
    public function createPaymentService(): PaymentService
    {
        return new PaymentService();
    }

    /**
     * Creates an instance of PickupPointService.
     *
     * @return PickupPointService A new instance of PickupPointService.
     */
    public function createPickupPointService(): PickupPointService
    {
        return new PickupPointService();
    }

    /**
     * Creates an instance of ContextService.
     *
     * @return ContextService A new instance of ContextService.
     */
    public function createContextService(): ContextService
    {
        return new ContextService();
    }

    /**
     * Creates an instance of AddressService.
     *
     * @return AddressService A new instance of AddressService.
     */
    public function createAddressService(): AddressService
    {
        return new AddressService();
    }
}