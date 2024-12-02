<?php

namespace Lemonade\EmailGenerator\DTO;

class AddressDTO
{
    public ?string $addressCompanyId = null;
    public ?string $addressCompanyVatId = null;
    public ?string $addressCompanyName = null;
    public ?string $addressAlias = null;
    public ?string $addressName = null;
    public ?string $addressStreet = null;
    public ?string $addressPostcode = null;
    public ?string $addressCity = null;
    public ?string $addressCountry = null;
    public ?string $addressPhone = null;
    public ?string $addressEmail = null;

    /**
     * Konstruktor, který inicializuje DTO adresy volitelným polem dat.
     *
     * @param array<string, mixed> $data Asociativní pole pro inicializaci dat.
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}


