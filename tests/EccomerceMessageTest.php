<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Order;

use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\Blocks\Order\EccomerceMessage;
use Lemonade\EmailGenerator\Context\ContextData;

class EccomerceMessageTest extends TestCase
{
    public function testEmptyMessage()
    {
        $messageBlock = new EccomerceMessage(null);
        $context = $messageBlock->getContext();

        $this->assertInstanceOf(ContextData::class, $context);
        $this->assertEquals("", $context->get("message"));
    }

    public function testStringMessage()
    {
        $message = "This is a test message.";
        $messageBlock = new EccomerceMessage($message);
        $context = $messageBlock->getContext();

        $this->assertEquals($message, $context->get("message"));
    }

    public function testArrayMessage()
    {
        $message = ["Line 1", "Line 2", "Line 3"];
        $expected = "Line 1\nLine 2\nLine 3";
        $messageBlock = new EccomerceMessage($message);
        $context = $messageBlock->getContext();

        $this->assertEquals($expected, $context->get("message"));
    }

    public function testMixedArrayMessage()
    {
        $message = ["Line 1", 42, null, 3.14, new class {
            public function __toString() {
                return "Object as string";
            }
        }];
        $expected = "Line 1\n42\n\n3.14\nObject as string";
        $messageBlock = new EccomerceMessage($message);
        $context = $messageBlock->getContext();

        $this->assertEquals($expected, $context->get("message"));
    }

    public function testInvalidTypeMessage()
    {
        $this->expectException(\TypeError::class);
        new EccomerceMessage(new \stdClass());
    }
}
