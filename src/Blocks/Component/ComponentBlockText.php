<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Blocks\BlockTrait;
use Lemonade\EmailGenerator\Services\ContextService;

class ComponentBlockText extends AbstractBlock
{
    use BlockTrait;

    /**
     * Constructor for `ComponentBlockText`.
     *
     * @param ContextService $contextData Context Service.
     * @param string|array|null $message The message content which can be a string, an array, or null.
     */
    public function __construct(protected readonly ContextService $contextService, string|array|null $message = null)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "message" => $this->getText($message)
        ]);

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
