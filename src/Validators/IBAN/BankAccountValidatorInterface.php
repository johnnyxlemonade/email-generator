<?php

namespace Lemonade\EmailGenerator\Validators\IBAN;

interface BankAccountValidatorInterface
{
    /**
     * Validates the account number stored in the object.
     *
     * @return bool True if the stored account number is valid, false otherwise.
     */
    public function isValid(): bool;

    /**
     * Generates an IBAN from a bank account number.
     *
     * @return string The generated IBAN.
     */
    public function generateIban(): string;

    /**
     * Retrieves the BIC for the account's bank code.
     *
     * @return string The BIC/SWIFT code.
     */
    public function getBic(): string;
}