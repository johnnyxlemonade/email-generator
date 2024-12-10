<?php

namespace Lemonade\EmailGenerator\Validators\IBAN;

abstract class AbstractBankAccountValidator implements BankAccountValidatorInterface
{

    protected ?string $bankAccount = null;
    protected ?string $bankPrefix = null;
    protected ?int $bankPrefixInt = null;
    protected ?string $bankNumber = null;
    protected ?int $bankNumberInt = null;
    protected ?string $bankCode = null;
    protected bool $ibanValid = false;

    /**
     * Constructor for the bank account validator.
     *
     * @param string $accountNumber The account number to validate and initialize.
     */
    public function __construct(string $accountNumber)
    {
        $this->initialize($accountNumber);
        $this->validateAccount();
    }

    /**
     * Checks if the stored bank account number is valid.
     *
     * @return bool True if the account number is valid, false otherwise.
     */
    public function isValid(): bool
    {
        return $this->ibanValid;
    }

    /**
     * Abstract method for initializing the bank account attributes.
     *
     * Each country should implement its own logic to parse and initialize the account number.
     *
     * @param string $accountNumber The account number to parse and initialize.
     * @return void
     */
    abstract protected function initialize(string $accountNumber): void;

    /**
     * Abstract method for validating the bank account.
     *
     * Each country should implement its own validation logic.
     *
     * @return void
     */
    abstract protected function validateAccount(): void;

    /**
     * Abstract method for generating an IBAN.
     *
     * Each country should implement its own IBAN generation logic.
     *
     * @return string The generated IBAN.
     */
    abstract public function generateIban(): string;

    /**
     * Pads a string on the left to the desired length.
     *
     * @param string|null $value The string to pad.
     * @param int $length The desired length of the string.
     * @return string The padded string.
     */
    protected function padLeft(?string $value, int $length): string
    {
        return str_pad($value ?? '', $length, '0', STR_PAD_LEFT);
    }

}