<?php

namespace Lemonade\EmailGenerator\Tests;

use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;

class ContextDataUpdateTest extends TestCase
{
    public function testUpdateContextValue(): void
    {
        // Create a ContextData instance with the original data
        $context = new ContextData(['key' => 'initialValue']);

        // Update value for existing key "key"
        $context->set('key', 'updatedValue');

        // Verifying that the value was updated correctly
        $this->assertSame('updatedValue', $context->get('key'));
    }
}
