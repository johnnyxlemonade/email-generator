<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\AddressDTO;
use Lemonade\EmailGenerator\Models\Address;

class AddressService
{
    public function getAddress(AddressDTO $data): Address
    {
        return new Address($data);
    }
}