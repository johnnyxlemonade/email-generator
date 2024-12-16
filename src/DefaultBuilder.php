<?php

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
use Lemonade\EmailGenerator\Blocks\Component\ComponentLostPassword;
use Lemonade\EmailGenerator\Blocks\Component\ComponentPickupPoint;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingFooter;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingHeader;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceAddress;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceCoupon;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceDelivery;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceHeader;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceMessage;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceNotifyAdministrator;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceNotifyCustomer;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceProductList;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceStatusButton;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceSummaryList;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingAddress;
use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Collection\CouponCollection;
use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Models\Address;
use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\Models\PickupPoint;
use Lemonade\EmailGenerator\Models\Shipping;

class DefaultBuilder
{

    private BlockManager $blockManager;

    /**
     * @param \Lemonade\EmailGenerator\ContainerBuilder $container
     * @param SupportedLanguage|null $language
     * @param SupportedCurrencies|null $currency
     * @param bool $center
     */
    public function __construct(
        protected readonly ContainerBuilder $container,
        ?SupportedLanguage $language = null,
        ?SupportedCurrencies $currency = null,
        bool $center = true
    ) {

        $this->blockManager = $this->container->getBlockManager();

        if ($center) {
            $this->blockManager->setBlockRenderCenter();
        }

        $this->blockManager->setCurrency(currency: $currency);

        // Override language if provided
        if ($language !== null) {
            $this->container->getTranslator()->setLanguage($language);
        }

    }

    /**
     * @return \Lemonade\EmailGenerator\ContainerBuilder
     */
    public function getContainerBuilder(): ContainerBuilder
    {
        return $this->container;
    }
    /**
     * @param string $title
     * @return $this
     */
    public function addHeader(string $title): self
    {
        $this->blockManager->setPageTitle($title);
        $this->blockManager->addBlock(new StaticBlockGreetingHeader());

        return $this;
    }

    /**
     * @param string|int $orderId
     * @param string|int $orderCode
     * @param string $orderDate
     * @param string|null $statusUrl
     * @return $this
     */
    public function addOrderInfo(string|int $orderId, string|int $orderCode, string $orderDate, ?string $statusUrl = null): self
    {
        $this->blockManager->addBlock(new EcommerceHeader(contextService: $this->container->getContextService(), orderId: $orderId, orderCode: $orderCode, orderDate: $orderDate));

        if($statusUrl !== null) {
            $this->blockManager->addBlock(block: new EcommerceStatusButton(url: $statusUrl));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addAdministratorNotify(): self
    {
        $this->blockManager->addBlock(new EcommerceNotifyAdministrator());
        return $this;
    }

    /**
     * @return $this
     */
    public function addCustomerNotify(): self
    {
        $this->blockManager->addBlock(new EcommerceNotifyCustomer());
        return $this;
    }

    /**
     * @param string $webName
     * @param string $anchorLink
     * @return $this
     */
    public function componentLostPassword(string $webName, string $anchorLink): self
    {
        $this->blockManager->addBlock(block: new ComponentLostPassword(contextService: $this->container->getContextService(), webName: $webName, anchorLink: $anchorLink));

        return $this;
    }

    /**
     * @param ProductCollection $productCollection
     * @return $this
     */
    public function addProducts(ProductCollection $productCollection): self
    {
        $this->blockManager->addBlock(new EcommerceProductList($this->container->getContextService(), $productCollection));
        return $this;
    }

    /**
     * @param PickupPoint $point
     * @return $this
     */
    public function addPickup(PickupPoint $point)
    {
        $this->blockManager->addBlock(block: new ComponentPickupPoint(contextService: $this->container->getContextService(), pickupPoint: $point));
        return $this;
    }

    /**
     * @param string|array|null $message
     * @return $this
     */
    public function addCustomerMessage(string|array|null $message = null): self
    {
        $this->blockManager->addBlock(block: new EcommerceMessage(contextService: $this->container->getContextService(), message: $message));
        return $this;
    }

    /**
     * @param CouponCollection $couponCollection
     * @return $this
     */
    public function addCoupon(CouponCollection $couponCollection): self
    {
        $this->blockManager->addBlock(new EcommerceCoupon($this->container->getContextService(), $couponCollection));
        return $this;
    }

    /**
     * @param AttachmentCollection $attachmentCollection
     * @return $this
     */
    public function addAttachments(AttachmentCollection $attachmentCollection): self
    {
        $this->blockManager->addBlock(new AttachmentList($this->container->getContextService(), $attachmentCollection));
        return $this;
    }

    /**
     * @param Address $billing
     * @param Address|null $shipping
     * @return $this
     */
    public function addBillingShippingAddress(Address $billing, ?Address $shipping = null): self
    {
        $this->blockManager->addBlock(new EcommerceAddress($this->container->getContextService(), $billing, $shipping));
        return $this;
    }

    /**
     * @param Shipping $shipping
     * @param Payment $payment
     * @return $this
     */
    public function addDelivery(Shipping $shipping, Payment $payment): self
    {
        $this->blockManager->addBlock(new EcommerceDelivery($this->container->getContextService(), $shipping, $payment));
        return $this;
    }

    /**
     * @param SummaryCollection $summaryCollection
     * @return $this
     */
    public function addSummary(SummaryCollection $summaryCollection): self
    {
        $this->blockManager->addBlock(new EcommerceSummaryList($this->container->getContextService(), $summaryCollection));
        return $this;
    }

    /**
     * @param Address $footerAddress
     * @return $this
     */
    public function addFooter(Address $footerAddress): self
    {
        $this->blockManager->addBlock(new StaticBlockGreetingFooter());
        $this->blockManager->addBlock(new StaticBlockGreetingAddress($this->container->getContextService(), $footerAddress));
        return $this;
    }

    /**
     * @return string
     */
    public function build(): string
    {
        return $this->blockManager->getHtml();
    }
}