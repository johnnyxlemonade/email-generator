<?php

namespace Lemonade\EmailGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\Blocks\Informational\StaticNotification;
use InvalidArgumentException;

class StaticNotificationTest extends TestCase
{
    /**
     * Testuje, zda je výchozí hodnota `heading` správně nastavena.
     */
    public function testHeadingAndNotificationAreSetCorrectly()
    {
        $heading = "Moje vlastní heading";
        $notification = "Moje vlastní notifikace!";
        $notificationBlock = new StaticNotification($heading, $notification);

        $context = $notificationBlock->getContext();

        $this->assertEquals($heading, $context->get("heading"));
        $this->assertEquals($notification, $context->get("notification"));
    }

    /**
     * Testuje, že validace kontextu neprojde, pokud chybí 'heading'.
     */
    public function testValidateContextWithMissingHeading()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Chybějící nebo prázdný požadovaný klíč: 'heading'.");

        $notificationBlock = new StaticNotification("", "Moje vlastní notifikace!");
        $notificationBlock->validateContext(); // Chybějící `heading` by měla vyvolat výjimku
    }

    /**
     * Testuje, že validace kontextu neprojde, pokud chybí 'notification'.
     */
    public function testValidateContextWithMissingNotification()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Chybějící nebo prázdný požadovaný klíč: 'notification'.");

        $notificationBlock = new StaticNotification("Moje vlastní heading", "");
        $notificationBlock->validateContext(); // Chybějící nebo prázdný `notification` by měla vyvolat výjimku
    }

    /**
     * Testuje, že validace kontextu probíhá správně, když jsou oba klíče nastavene.
     */
    public function testValidateContextWithValidData()
    {
        $heading = "Moje vlastní heading";
        $notification = "Moje vlastní notifikace!";

        $notificationBlock = new StaticNotification($heading, $notification);

        // Pokud je všechno v pořádku, validace nevyvolá žádnou výjimku
        $notificationBlock->validateContext();

        $this->assertTrue(true); // Pokud validace nevyvolí výjimku, test projde
    }
}




