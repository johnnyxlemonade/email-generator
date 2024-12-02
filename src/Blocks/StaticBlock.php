<?php

namespace Lemonade\EmailGenerator\Blocks;

use Lemonade\EmailGenerator\Context\ContextData;

class StaticBlock extends AbstractBlock
{
    /**
     * @var string Název šablonového souboru (bez přípony).
     */
    protected string $template;

    /**
     * Konstruktor pro `StaticBlock`.
     *
     * @param string $template Název šablonového souboru (bez přípony).
     */
    public function __construct(string $template)
    {
        $this->template = $template;
        parent::__construct(new ContextData(), $template);
    }

    /**
     * Vrací název šablony.
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return void
     */
    public function validateContext(): void
    {
        // Žádná validace není nutná
    }
}