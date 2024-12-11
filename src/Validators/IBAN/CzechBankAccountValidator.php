<?php

namespace Lemonade\EmailGenerator\Validators\IBAN;

/**
 * Class CzechBankAccountValidator
 * Provides validation and manipulation for bank accounts.
 */
class CzechBankAccountValidator extends AbstractBankAccountValidator
{

    /**
     * @var string
     */
    public const CODE = "CZ";

    /**
     * @var array<int>
     */
    protected array $prefixWeight = [10, 5, 8, 4, 2, 1]; // Same as default, can be omitted if unchanged

    /**
     * @var array<int>
     */
    protected array $numberWeight = [6, 3, 7, 9, 10, 5, 8, 4, 2, 1]; // Same as default, can be omitted if unchanged


    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return self::CODE;
    }

    /**
     * @param string $number
     * @return void
     */
    protected function initializeClass(string $number): void
    {
        if (preg_match('/^((\d{0,6})-)?(\d{2,10})\/(\d{4})$/', $number, $matchList) === 1) {
            $this->accountPrefix = $this->padLeft($matchList[2] ?? '', 6);
            $this->accountNumber = $this->padLeft($matchList[3], 10);
            $this->accountCode = $this->padLeft($matchList[4], 4);
            $this->accountFull = $this->accountPrefix . '-' . $this->accountNumber . '/' . $this->accountCode;
        }
    }

}
