<?php

namespace Lemonade\EmailGenerator\Blocks;

use Lemonade\EmailGenerator\Context\ContextData;

class StaticBlock extends AbstractBlock
{
    /**
     * @var string Template file name (without extension).
     */
    protected string $template;

    /**
     * Constructor for `StaticBlock`.
     *
     * @param string $template Template file name (without extension).
     */
    public function __construct(string $template)
    {
        $this->template = $template;
        parent::__construct(new ContextData(), $template);
    }

    /**
     * Returns the template name.
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
        // No validation needed
    }
}
