<?php

namespace Lemonade\EmailGenerator\Blocks\Order;
use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EccomerceNotify extends AbstractBlock
{

    /**
     * @param ContextData $context
     */
    public function __construct(ContextData $context)
    {

        parent::__construct($context);
    }

    /**
     * Validace požadovaných klíčů v contextu.
     *
     * @return void
     */
    protected function validateContext(): void
    {
        // Seznam požadovaných klíčů pro tento blok
        $this->validateRequiredKeys(["webName"]);
    }

}