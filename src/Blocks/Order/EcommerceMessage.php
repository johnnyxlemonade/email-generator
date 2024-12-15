<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Blocks\BlockTrait;
use Lemonade\EmailGenerator\Services\ContextService;

class EcommerceMessage extends AbstractBlock
{
    use BlockTrait;

    /**
     * Constructor for `EcommerceMessage`.
     *
     * @param ContextService $contextService Context Service.
     * @param string|array|null $message The message content, which can be a string, array, or null.
     */
    public function __construct(protected readonly ContextService $contextService, string|array|null $message = null)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "message" => $this->getText($message)
        ]);

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
