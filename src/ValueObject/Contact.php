<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * Validated identity and contact data for a person
 */
final readonly class Contact
{
    private string|Translation|null $position;

    private string|Translation|null $organization;

    private ?string $email;

    private ?string $phone;

    /**
     * Validates identity text and normalizes blank optional details
     */
    public function __construct(
        private string|Translation $name,
        string|Translation|null $position = null,
        string|Translation|null $organization = null,
        ?string $email = null,
        ?string $phone = null,
    ) {
        InputValidator::text($name);

        $this->position = self::optionalTranslatableText($position);
        $this->organization = self::optionalTranslatableText($organization);
        $this->email = InputValidator::optionalText($email);
        $this->phone = InputValidator::optionalText($phone);
    }

    /**
     * Returns the contact name
     */
    public function name(): string|Translation
    {
        return $this->name;
    }

    /**
     * Returns the optional position
     */
    public function position(): string|Translation|null
    {
        return $this->position;
    }

    /**
     * Returns the optional organization
     */
    public function organization(): string|Translation|null
    {
        return $this->organization;
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

    /**
     * Treats null and blank literal text as absent after validation
     */
    private static function optionalTranslatableText(string|Translation|null $value): string|Translation|null
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            return null;
        }

        InputValidator::text($value);

        return $value;
    }
}
