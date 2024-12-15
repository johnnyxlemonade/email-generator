<?php

namespace Lemonade\EmailGenerator\Blocks;

trait BlockTrait
{
    /**
     * Processes the input message and returns it as a string or null.
     *
     * @param string|int|float|array<int,string|int|float|null>|null $message The message to process.
     * @return string|null The processed message content.
     */
    public function getText(string|int|float|array|null $message = null): ?string
    {
        // Default value for null messages
        $message ??= "";

        // If the message is an array, join its elements into a single string
        if (is_array($message)) {
            // Convert all elements to string
            $message = array_map(fn($item) => (string)$item, $message);
            $message = implode("\n", $message);
        } else {
            // Convert single value to string
            $message = (string)$message;
        }

        return $message;
    }
}