<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\AddressDTO;
use Lemonade\EmailGenerator\Models\Address;

class AddressService
{
    /**
     * Retrieves an Address instance from the given AddressDTO.
     *
     * @param AddressDTO $data Data Transfer Object (DTO) containing address information.
     * @return Address A new Address instance based on the provided DTO.
     */
    public function getAddress(AddressDTO $data): Address
    {
        return new Address($data);
    }
}
