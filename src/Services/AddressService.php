<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\DTO\AddressData;
use Lemonade\EmailGenerator\Models\Address;

class AddressService
{
    /**
     * Retrieves an Address instance from the given AddressData.
     *
     * @param AddressData $data Data Transfer Object (DTO) containing address information.
     * @return Address A new Address instance based on the provided DTO.
     */
    public function getAddress(AddressData $data): Address
    {
        return new Address($data);
    }
}
