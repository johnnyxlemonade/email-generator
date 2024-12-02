<?php

namespace Lemonade\EmailGenerator\Models;

use Lemonade\EmailGenerator\DTO\AddressDTO;

class Address
{
    private AddressDTO $data;

    /**
     * Konstruktor, který inicializuje instanci `Address` pomocí `AddressDTO`.
     *
     * @param AddressDTO|null $data DTO s daty adresy.
     */
    public function __construct(?AddressDTO $data = null)
    {
        $this->data = $data ?? new AddressDTO();
    }

    /**
     * Nastaví data adresy pomocí DTO.
     *
     * @param AddressDTO $data DTO s daty adresy.
     * @return self
     */
    public function setAddress(AddressDTO $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Vrací data adresy jako DTO.
     *
     * @return AddressDTO
     */
    public function getAddress(): AddressDTO
    {
        return $this->data;
    }

    /**
     * Vytvoří kopii aktuální adresy.
     *
     * @return Address Nová instance `Address` s kopií dat.
     */
    public function copy(): Address
    {
        // Vytvoření nové instance Address s kopií AddressDTO
        $copiedData = new AddressDTO((array)$this->data);
        return new self($copiedData);
    }

    /**
     * Vrátí IČ společnosti.
     *
     * @return string|null IČ nebo null, pokud není nastaveno.
     */
    public function getCompanyId(): ?string
    {
        return $this->data->addressCompanyId;
    }

    /**
     * Nastaví IČ společnosti.
     *
     * @param string|null $companyId IČ společnosti.
     * @return void
     */
    public function setCompanyId(?string $companyId): void
    {
        $this->data->addressCompanyId = $companyId;
    }

    /**
     * Vrátí DIČ společnosti.
     *
     * @return string|null DIČ nebo null, pokud není nastaveno.
     */
    public function getCompanyVatId(): ?string
    {
        return $this->data->addressCompanyVatId;
    }

    /**
     * Nastaví DIČ společnosti.
     *
     * @param string|null $companyVatId DIČ společnosti.
     * @return void
     */
    public function setCompanyVatId(?string $companyVatId): void
    {
        $this->data->addressCompanyVatId = $companyVatId;
    }

    /**
     * Vrátí název společnosti.
     *
     * @return string|null Název společnosti nebo null, pokud není nastaveno.
     */
    public function getCompanyName(): ?string
    {
        return $this->data->addressCompanyName;
    }

    /**
     * Nastaví název společnosti.
     *
     * @param string|null $companyName Název společnosti.
     * @return void
     */
    public function setCompanyName(?string $companyName): void
    {
        $this->data->addressCompanyName = $companyName;
    }

    /**
     * Vrátí alias adresy.
     *
     * @return string|null Alias nebo null, pokud není nastaven.
     */
    public function getAddressAlias(): ?string
    {
        return $this->data->addressAlias;
    }

    /**
     * Nastaví alias adresy.
     *
     * @param string|null $alias Alias adresy.
     * @return void
     */
    public function setAddressAlias(?string $alias): void
    {
        $this->data->addressAlias = $alias;
    }

    /**
     * Vrátí jméno osoby na adrese.
     *
     * @return string|null Jméno nebo null, pokud není nastaveno.
     */
    public function getAddressName(): ?string
    {
        return $this->data->addressName;
    }

    /**
     * Nastaví jméno osoby na adrese.
     *
     * @param string|null $name Jméno osoby.
     * @return void
     */
    public function setAddressName(?string $name): void
    {
        $this->data->addressName = $name;
    }

    /**
     * Vrátí ulici adresy.
     *
     * @return string|null Ulice nebo null, pokud není nastavena.
     */
    public function getAddressStreet(): ?string
    {
        return $this->data->addressStreet;
    }

    /**
     * Nastaví ulici adresy.
     *
     * @param string|null $street Ulice.
     * @return void
     */
    public function setAddressStreet(?string $street): void
    {
        $this->data->addressStreet = $street;
    }

    /**
     * Vrátí PSČ adresy.
     *
     * @return string|null PSČ nebo null, pokud není nastaveno.
     */
    public function getAddressPostcode(): ?string
    {
        return $this->data->addressPostcode;
    }

    /**
     * Nastaví PSČ adresy.
     *
     * @param string|null $postcode PSČ.
     * @return void
     */
    public function setAddressPostcode(?string $postcode): void
    {
        $this->data->addressPostcode = $postcode;
    }

    /**
     * Vrátí město adresy.
     *
     * @return string|null Město nebo null, pokud není nastaveno.
     */
    public function getAddressCity(): ?string
    {
        return $this->data->addressCity;
    }

    /**
     * Nastaví město adresy.
     *
     * @param string|null $city Město.
     * @return void
     */
    public function setAddressCity(?string $city): void
    {
        $this->data->addressCity = $city;
    }

    /**
     * Vrátí zemi adresy.
     *
     * @return string|null Země nebo null, pokud není nastavena.
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
     * Nastaví zemi adresy.
     *
     * @param string|null $country Země.
     * @return void
     */
    public function setAddressCountry(?string $country): void
    {
        $this->data->addressCountry = $country;
    }

    /**
     * Vrátí telefon na adrese.
     *
     * @return string|null Telefon nebo null, pokud není nastaven.
     */
    public function getAddressPhone(): ?string
    {
        return $this->data->addressPhone;
    }

    /**
     * Nastaví telefon na adrese.
     *
     * @param string|null $phone Telefon.
     * @return void
     */
    public function setAddressPhone(?string $phone): void
    {
        $this->data->addressPhone = $phone;
    }

    /**
     * Vrátí emailovou adresu.
     *
     * @return string|null Email nebo null, pokud není nastaven.
     */
    public function getAddressEmail(): ?string
    {
        return $this->data->addressEmail;
    }

    /**
     * Nastaví emailovou adresu.
     *
     * @param string|null $email Email.
     * @return void
     */
    public function setAddressEmail(?string $email): void
    {
        $this->data->addressEmail = $email;
    }

    /**
     * Vrátí data jako asociativní pole.
     *
     * @return array<string, mixed> Asociativní pole s daty adresy.
     */
    public function toArray(): array
    {
        return get_object_vars($this->data);
    }
}

