<?php

namespace Lemonade\EmailGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Collection\FormItemCollection;
use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\DTO\FormItemData;
use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Models\Attachment;
use Lemonade\EmailGenerator\Models\FormItem;
use Lemonade\EmailGenerator\Models\SummaryItem;
use Lemonade\EmailGenerator\Services\PaymentService;
use Lemonade\EmailGenerator\Services\ShippingService;
use Lemonade\EmailGenerator\DTO\PaymentData;
use Lemonade\EmailGenerator\DTO\ShippingData;

class CollectionServiceTests extends TestCase
{
    public function testAttachmentCollection(): void
    {
        $collection = new AttachmentCollection();

        $attachmentData = new AttachmentData('file1', 'https://example.com/file1', '1024', 'pdf');
        $attachment = new Attachment(
            $attachmentData->name,
            $attachmentData->link,
            $attachmentData->size,
            $attachmentData->extension
        );

        $collection->add($attachment);

        $this->assertCount(1, $collection);
        $this->assertSame($attachment, $collection->get(0));
    }

    public function testFormItemCollection(): void
    {
        $collection = new FormItemCollection();

        $formItemData = new FormItemData('username', 'JohnDoe');
        $formItem = new FormItem($formItemData->name, $formItemData->value);

        $collection->add($formItem);

        $this->assertCount(1, $collection);
        $this->assertSame($formItem, $collection->get(0));
    }

    public function testSummaryCollection(): void
    {
        $collection = new SummaryCollection();

        $summaryData = new SummaryData('Subtotal', 200.50, false);
        $summaryItem = new SummaryItem($summaryData->name, $summaryData->value, $summaryData->final);

        $collection->add($summaryItem);

        $this->assertCount(1, $collection);
        $this->assertSame($summaryItem, $collection->get(0));
    }

    public function testPaymentService(): void
    {
        $paymentData = new PaymentData('Credit Card', 10.0, true);
        $paymentService = new PaymentService();
        $payment = $paymentService->createPayment($paymentData);

        $this->assertSame($paymentData->name, $payment->getName());
        $this->assertSame($paymentData->price, $payment->getPrice());
        $this->assertTrue($payment->shouldDisplay());
    }

    public function testShippingService(): void
    {
        $shippingData = new ShippingData('DHL', 15.5, true);
        $shippingService = new ShippingService();
        $shipping = $shippingService->createShipping($shippingData);

        $this->assertSame($shippingData->name, $shipping->getName());
        $this->assertSame($shippingData->price, $shipping->getPrice());
        $this->assertTrue($shipping->shouldDisplay());
    }
}
