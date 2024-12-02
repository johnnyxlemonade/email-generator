<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Context\ContextData;

class ContextService
{
    public function createContext(array $data = []): ContextData
    {
        return new ContextData($data);
    }
}