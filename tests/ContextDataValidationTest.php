<?php

namespace Lemonade\EmailGenerator\Tests;

use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class ContextDataValidationTest extends TestCase
{
    public function testMissingRequiredKeyInContext(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing or empty required key: 'requiredKey'.");

        // Vytvoření instance ContextData bez nastavení klíče "requiredKey"
        $context = new ContextData();

        // Pokus o validaci s chybějícím klíčem
        $context->validate(['requiredKey']);
    }
}