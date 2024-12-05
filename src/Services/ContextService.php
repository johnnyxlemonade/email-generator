<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Context\ContextData;

class ContextService
{
    /**
     * Creates a new ContextData instance from the provided data.
     *
     * @param array $data Optional data to initialize the context.
     * @return ContextData A new instance of ContextData.
     */
    public function createContext(array $data = []): ContextData
    {
        return new ContextData($data);
    }
}
