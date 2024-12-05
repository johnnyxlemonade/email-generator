<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class ComponentNotification extends AbstractBlock
{
    /**
     * Constructor for `ComponentNotification`.
     *
     * @param string|null $heading The heading of the notification.
     * @param string|null $notification The notification message.
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
     * Validate the required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that "heading" and "notification" keys are present in the context
        $this->context->validate(["heading", "notification"]);
    }
}
