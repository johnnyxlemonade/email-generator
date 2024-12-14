<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EcommerceStatusButton extends AbstractBlock
{

    /**
     * Constructor for `EcommerceStatusButton`.
     *
     * @param string $url URL adress.
     */
    public function __construct(string $url)
    {
        // Initialize context
        $context = new ContextData();

        // Add addresses to context
        $context->set("buttonLink", $url);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Assume `buttonLink` is already set in the context
        $this->context->validate(["buttonLink"]);
    }
}