<?php

namespace Lemonade\EmailGenerator\DTO;

class AddressData
{
    /**
     * @var ?string The company ID for the address.
     */
    public ?string $addressCompanyId = null;

    /**
     * @var ?string The company VAT ID for the address.
     */
    public ?string $addressCompanyVatId = null;

    /**
     * @var ?string The company name for the address.
     */
    public ?string $addressCompanyName = null;

    /**
     * @var ?string An alias for the address.
     */
    public ?string $addressAlias = null;

    /**
     * @var ?string The name associated with the address.
     */
    public ?string $addressName = null;

    /**
     * @var ?string The street address.
     */
    public ?string $addressStreet = null;

    /**
     * @var ?string The postcode for the address.
     */
    public ?string $addressPostcode = null;

    /**
     * @var ?string The city for the address.
     */
    public ?string $addressCity = null;

    /**
     * @var ?string The country for the address.
     */
    public ?string $addressCountry = null;

    /**
     * @var ?string The phone number for the address.
     */
    public ?string $addressPhone = null;

    /**
     * @var ?string The email for the address.
     */
    public ?string $addressEmail = null;

    /**
     * Constructor for AddressDTO.
     * Initializes the address data using an associative array.
     *
     * @param array<string, mixed> $data Associative array for initializing the data.
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
