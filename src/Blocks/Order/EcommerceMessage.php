<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EcommerceMessage extends AbstractBlock
{
    /**
     * Constructor for `EcommerceMessage`.
     *
     * @param string|array|null $message The message content, which can be a string, array, or null.
     */
    public function __construct(string|array|null $message = null)
    {
        // Initialize the context
        $context = new ContextData();

        // Validate the type of the message and set a default value if not provided
        if (is_null($message)) {
            $message = "";
        }

        // If the message is an array, convert all elements to strings and join them into a single string
        if (is_array($message)) {
            // Filter only values that can be converted to strings
            $message = array_filter($message, fn($item) => is_scalar($item) || is_null($item) || (is_object($item) && method_exists($item, '__toString')));
            $message = array_map('strval', $message);
            $message = implode("\n", $message);
        }

        // Add the message to the context
        $context->set("message", $message);

        // Pass the context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validation of required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // List of required keys for this block
        // $this->validateRequiredKeys(["message"]);
    }
}
