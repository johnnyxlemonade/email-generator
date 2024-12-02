<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Context\ContextData;

class AttachmentList extends AbstractBlock
{
    /**
     * @param AttachmentCollection $collection
     */
    public function __construct(AttachmentCollection $collection)
    {
        // Inicializace kontextu
        $context = new ContextData();
        $context->set("attachments", $collection->all());

        // Předání kontextu do rodičovského konstruktoru
        parent::__construct($context);
    }

    /**
     * @return void
     */
    protected function validateContext(): void
    {
        // Předpokládáme, že `attachments` jsou už v kontextu nastaveny
        $this->context->validate(["attachments"]);
    }
}