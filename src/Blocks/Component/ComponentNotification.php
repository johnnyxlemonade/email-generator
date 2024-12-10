<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

/**
 * Class ComponentNotification
 * Represents a block component for displaying a notification with a heading and message.
 */
class ComponentNotification extends AbstractBlock
{
    /**
     * Constructor for `ComponentNotification`.
     *
     * Initializes the block with a heading and a notification message.
     *
     * @param string $heading The heading of the notification.
     * @param string $notification The notification message.
     */
    public function __construct(string $heading, string $notification)
    {
        // Initialize context
        $context = new ContextData();

        // Add heading and notification to context
        $context->set("heading", $heading);
        $context->set("notification", $notification);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the required keys in the context.
     *
     * Ensures that "heading" and "notification" keys are present in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that "heading" and "notification" keys are present in the context
        $this->context->validate(["heading", "notification"]);
    }
}
