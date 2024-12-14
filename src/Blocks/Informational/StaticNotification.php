<?php

namespace Lemonade\EmailGenerator\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Services\ContextService;

class StaticNotification extends AbstractBlock
{
    /**
     * Constructor for `StaticNotification`.
     *
     * @param ContextService $contextData Context Service.
     * @param string|null $heading The heading for the notification.
     * @param string|null $notification The content of the notification.
     */
    public function __construct(protected readonly ContextService $contextService, string $heading, string $notification)
    {
        // Initialize context
        $context = $this->contextService->createContext([
            "heading" => $heading,
            "notification" => $notification,
        ]);

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
