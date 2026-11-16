<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Contract;

/**
 * Expands custom content into built-in components or further logical compositions
 */
interface CompositionInterface extends ComponentInterface
{
    /**
     * Returns child components in expansion order for snapshotting into a document
     *
     * @return iterable<ComponentInterface>
     */
    public function components(): iterable;
}
