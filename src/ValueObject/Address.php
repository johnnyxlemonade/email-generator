<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * Validated postal, company and contact address data
 */
final readonly class Address
{
    private ?string $companyId;

    private ?string $taxId;

    private ?string $email;

    private ?string $phone;

    /**
     * Validates required address text and normalizes blank optional details
     */
    public function __construct(
        private string|Translation $name,
        private string|Translation $street,
        private string|Translation $city,
        private string|Translation $postalCode,
        private string|Translation $country,
        ?string $companyId = null,
        ?string $taxId = null,
        ?string $email = null,
        ?string $phone = null,
    ) {
        InputValidator::text($name);
        InputValidator::text($street);
        InputValidator::text($city);
        InputValidator::text($postalCode);
        InputValidator::text($country);

        $this->companyId = InputValidator::optionalText($companyId);
        $this->taxId = InputValidator::optionalText($taxId);
        $this->email = InputValidator::optionalText($email);
        $this->phone = InputValidator::optionalText($phone);
    }

    /**
     * Returns the person or organization name
     */
    public function name(): string|Translation
    {
        return $this->name;
    }

    /**
     * Returns the street address
     */
    public function street(): string|Translation
    {
        return $this->street;
    }

    /**
     * Returns the city or locality
     */
    public function city(): string|Translation
    {
        return $this->city;
    }

    /**
     * Returns the postal code
     */
    public function postalCode(): string|Translation
    {
        return $this->postalCode;
    }

    /**
     * Returns the country name
     */
    public function country(): string|Translation
    {
        return $this->country;
    }

    /**
     * Returns the optional company registration identifier
     */
    public function companyId(): ?string
    {
        return $this->companyId;
    }

    /**
     * Returns the optional tax identifier
     */
    public function taxId(): ?string
    {
        return $this->taxId;
    }

    /**
     * Returns the optional plain-text email address
     */
    public function email(): ?string
    {
        return $this->email;
    }

    /**
     * Returns the optional plain-text telephone number
     */
    public function phone(): ?string
    {
        return $this->phone;
    }
}
