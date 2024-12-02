<?php

namespace Lemonade\EmailGenerator\Blocks\Component;
use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class ComponentBlockText extends AbstractBlock
{

    /**
     * @param ContextData $context
     */
    public function __construct(ContextData $context)
    {

        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function validateContext(): void
    {
        // Validace očekávaných klíčů
        $this->context->validate(["label", "description", "content"]);
    }

}