<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingHeader;
use PHPUnit\Framework\TestCase;

class StaticBlockGreetingHeaderTest extends TestCase
{
    private StaticBlockGreetingHeader $staticBlockGreetingHeader;

    /**
     * Inicializace StaticBlockGreetingHeader před každým testem.
     */
    protected function setUp(): void
    {
        // Vytváříme instanci třídy StaticBlockGreetingHeader
        $this->staticBlockGreetingHeader = new StaticBlockGreetingHeader();
    }

    /**
     * Testuje inicializaci StaticBlockGreetingHeader a ověřuje, že název šablony je správný.
     */
    public function testInitialization(): void
    {
        // Ověřujeme, že název šablony odpovídá očekávané hodnotě
        $this->assertEquals('StaticBlockGreetingHeader', $this->staticBlockGreetingHeader->getTemplate());
    }
}
