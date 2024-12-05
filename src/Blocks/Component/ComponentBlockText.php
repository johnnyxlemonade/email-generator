<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class ComponentBlockText extends AbstractBlock
{
    /**
     * Constructor for `ComponentBlockText`.
     *
     * @param string|array|null $message The message content which can be a string, an array, or null.
     */
    public function __construct(string|array|null $message = null)
    {
        // Initialize context
        $context = new ContextData();

        // If the message is null, set it to an empty string
        if (is_null($message)) {
            $message = "";
        }

        // If the message is an array, convert each element to a string and concatenate them
        if (is_array($message)) {
            // Filter only scalar values or objects with a __toString method
            $message = array_filter($message, fn($item) => is_scalar($item) || is_null($item) || (is_object($item) && method_exists($item, '__toString')));
            $message = array_map('strval', $message);
            $message = implode("\n", $message);
        }

        // Add the message to the context
        $context->set("message", $message);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validate required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that the "message" key is present in the context
        $this->validateRequiredKeys(["message"]);
    }
}
