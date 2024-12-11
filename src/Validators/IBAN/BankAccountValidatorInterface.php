<?php

namespace Lemonade\EmailGenerator\Validators\IBAN;

interface BankAccountValidatorInterface
{
    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return string
     */
    public function getBankName(): string;

    /**
     * @return string|null
     */
    public function getAccountNumber(): ?string;

    /**
     * @return string
     */
    public function getIban(): string;

    /**
     * @return string
     */
    public function getBankSwift(): string;

    /**
     * @return string
     */
    public function getCountryCode(): string;
}