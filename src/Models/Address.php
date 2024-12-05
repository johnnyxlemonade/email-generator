<?php

namespace Lemonade\EmailGenerator\Models;

use Lemonade\EmailGenerator\DTO\AddressDTO;

class Address
{
    private AddressDTO $data;

    /**
     * Constructor that initializes an `Address` instance using `AddressDTO`.
     *
     * @param AddressDTO|null $data DTO with address data.
     */
    public function __construct(?AddressDTO $data = null)
    {
        $this->data = $data ?? new AddressDTO();
    }

    /**
     * Sets the address data using DTO.
     *
     * @param AddressDTO $data DTO with address data.
     * @return self
     */
    public function setAddress(AddressDTO $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Returns the address data as DTO.
     *
     * @return AddressDTO
     */
    public function getAddress(): AddressDTO
    {
        return $this->data;
    }

    /**
     * Creates a copy of the current address.
     *
     * @return Address New instance of `Address` with copied data.
     */
    public function copy(): Address
    {
        // Create a new instance of Address with a copy of AddressDTO
        $copiedData = new AddressDTO((array)$this->data);
        return new self($copiedData);
    }

    /**
     * Returns the company ID.
     *
     * @return string|null Company ID or null if not set.
     */
    public function getCompanyId(): ?string
    {
        return $this->data->addressCompanyId;
    }

    /**
     * Sets the company ID.
     *
     * @param string|null $companyId Company ID.
     * @return void
     */
    public function setCompanyId(?string $companyId): void
    {
        $this->data->addressCompanyId = $companyId;
    }

    /**
     * Returns the company VAT ID.
     *
     * @return string|null Company VAT ID or null if not set.
     */
    public function getCompanyVatId(): ?string
    {
        return $this->data->addressCompanyVatId;
    }

    /**
     * Sets the company VAT ID.
     *
     * @param string|null $companyVatId Company VAT ID.
     * @return void
     */
    public function setCompanyVatId(?string $companyVatId): void
    {
        $this->data->addressCompanyVatId = $companyVatId;
    }

    /**
     * Returns the company name.
     *
     * @return string|null Company name or null if not set.
     */
    public function getCompanyName(): ?string
    {
        return $this->data->addressCompanyName;
    }

    /**
     * Sets the company name.
     *
     * @param string|null $companyName Company name.
     * @return void
     */
    public function setCompanyName(?string $companyName): void
    {
        $this->data->addressCompanyName = $companyName;
    }

    /**
     * Returns the address alias.
     *
     * @return string|null Alias or null if not set.
     */
    public function getAddressAlias(): ?string
    {
        return $this->data->addressAlias;
    }

    /**
     * Sets the address alias.
     *
     * @param string|null $alias Address alias.
     * @return void
     */
    public function setAddressAlias(?string $alias): void
    {
        $this->data->addressAlias = $alias;
    }

    /**
     * Returns the name of the person at the address.
     *
     * @return string|null Name or null if not set.
     */
    public function getAddressName(): ?string
    {
        return $this->data->addressName;
    }

    /**
     * Sets the name of the person at the address.
     *
     * @param string|null $name Name of the person.
     * @return void
     */
    public function setAddressName(?string $name): void
    {
        $this->data->addressName = $name;
    }

    /**
     * Returns the street of the address.
     *
     * @return string|null Street or null if not set.
     */
    public function getAddressStreet(): ?string
    {
        return $this->data->addressStreet;
    }

    /**
     * Sets the street of the address.
     *
     * @param string|null $street Street.
     * @return void
     */
    public function setAddressStreet(?string $street): void
    {
        $this->data->addressStreet = $street;
    }

    /**
     * Returns the postcode of the address.
     *
     * @return string|null Postcode or null if not set.
     */
    public function getAddressPostcode(): ?string
    {
        return $this->data->addressPostcode;
    }

    /**
     * Sets the postcode of the address.
     *
     * @param string|null $postcode Postcode.
     * @return void
     */
    public function setAddressPostcode(?string $postcode): void
    {
        $this->data->addressPostcode = $postcode;
    }

    /**
     * Returns the city of the address.
     *
     * @return string|null City or null if not set.
     */
    public function getAddressCity(): ?string
    {
        return $this->data->addressCity;
    }

    /**
     * Sets the city of the address.
     *
     * @param string|null $city City.
     * @return void
     */
    public function setAddressCity(?string $city): void
    {
        $this->data->addressCity = $city;
    }

    /**
     * Returns the country of the address.
     *
     * @return string|null Country or null if not set.
     */
    public function getAddressCountry(): ?string
    {
        return match ($this->data->addressCountry) {
            "56", "CZ" => "Česká republika",
            "202", "SVK", "SK" => "Slovensko",
            default => $this->data->addressCountry,
        };
    }

    /**
     * Sets the country of the address.
     *
     * @param string|null $country Country.
     * @return void
     */
    public function setAddressCountry(?string $country): void
    {
        $this->data->addressCountry = $country;
    }

    /**
     * Returns the phone number of the address.
     *
     * @return string|null Phone number or null if not set.
     */
    public function getAddressPhone(): ?string
    {
        return $this->data->addressPhone;
    }

    /**
     * Sets the phone number of the address.
     *
     * @param string|null $phone Phone number.
     * @return void
     */
    public function setAddressPhone(?string $phone): void
    {
        $this->data->addressPhone = $phone;
    }

    /**
     * Returns the email address.
     *
     * @return string|null Email or null if not set.
     */
    public function getAddressEmail(): ?string
    {
        return $this->data->addressEmail;
    }

    /**
     * Sets the email address.
     *
     * @param string|null $email Email.
     * @return void
     */
    public function setAddressEmail(?string $email): void
    {
        $this->data->addressEmail = $email;
    }

    /**
     * Returns the address data as an associative array.
     *
     * @return array<string, mixed> Associative array with address data.
     */
    public function toArray(): array
    {
        return get_object_vars($this->data);
    }
}
