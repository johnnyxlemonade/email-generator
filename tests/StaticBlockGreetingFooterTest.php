<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingFooter;
use PHPUnit\Framework\TestCase;

class StaticBlockGreetingFooterTest extends TestCase
{
    private StaticBlockGreetingFooter $staticBlockGreetingFooter;

    /**
     * Inicializace StaticBlockGreetingFooter před každým testem.
     */
    protected function setUp(): void
    {
        // Vytváříme instanci třídy StaticBlockGreetingFooter
        $this->staticBlockGreetingFooter = new StaticBlockGreetingFooter();
    }

    /**
     * Testuje inicializaci StaticBlockGreetingFooter a ověřuje, že název šablony je správný.
     */
    public function testInitialization(): void
    {
        // Ověřujeme, že název šablony odpovídá očekávané hodnotě
        $this->assertEquals('StaticBlockGreetingFooter', $this->staticBlockGreetingFooter->getTemplate());
    }
}
