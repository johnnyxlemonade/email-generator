<?php

namespace Lemonade\EmailGenerator\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class StaticNotification extends AbstractBlock
{
    /**
     * Constructor for `StaticNotification`.
     *
     * @param string|null $heading The heading for the notification.
     * @param string|null $notification The content of the notification.
     */
    public function __construct(string $heading, string $notification)
    {
        // Initialize context
        $context = new ContextData();

        // Set heading and notification in context
        $context->set("heading", $heading);
        $context->set("notification", $notification);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validate context for the block.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Ensure `heading` and `notification` keys are set in the context
        $this->context->validate(["heading", "notification"]);
    }
}
