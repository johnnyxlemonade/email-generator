<?php

namespace Lemonade\EmailGenerator\Validators\IBAN;
use Psr\Log\LoggerInterface;

class FormatGenerator
{

    /**
     * Predefined IBAN lengths for supported countries.
     * Source: https://www.iban.com/structure
     *
     * @var array|int[]
     */
    private static array $ibanLengths = [
        'AL' => 28, 'AD' => 24, 'AT' => 20, 'AZ' => 28, 'BE' => 16,
        'BA' => 20, 'BG' => 22, 'HR' => 21, 'CY' => 28, 'CZ' => 24,
        'DK' => 18, 'EE' => 20, 'FO' => 18, 'FI' => 18, 'FR' => 27,
        'GE' => 22, 'DE' => 22, 'GI' => 23, 'GR' => 27, 'GL' => 18,
        'HU' => 28, 'IS' => 26, 'IE' => 22, 'IT' => 27, 'LV' => 21,
        'LI' => 21, 'LT' => 20, 'LU' => 20, 'MT' => 31, 'MC' => 27,
        'ME' => 22, 'NL' => 18, 'NO' => 15, 'PL' => 28, 'PT' => 25,
        'RO' => 24, 'SM' => 27, 'RS' => 22, 'SK' => 24, 'SI' => 19,
        'ES' => 24, 'SE' => 24, 'CH' => 21, 'TR' => 26, 'GB' => 22,
        'XK' => 20
    ];

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger Logger instance for logging errors and debug information.
     */
    public function __construct(protected readonly LoggerInterface $logger)
    {
    }

    /**
     * Generates an IBAN code based on the given country, bank, and account information.
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code.
     * @param string $bankCode Bank code (numeric).
     * @param string $accountNumber Account number (numeric).
     * @param string $bankPrefix Optional bank prefix for countries like the Czech Republic.
     * @return string The generated IBAN code or an empty string if invalid inputs are provided.
     */
    public function generateCode(string $countryCode, string $bankCode, string $accountNumber, string $bankPrefix = ""): string
    {
        $countryCode = strtoupper($countryCode);

        // Validate inputs
        if (!$this->isValidCountryCode($countryCode)) {
            $this->logger->error("Unsupported or invalid country code: $countryCode");
            return "";
        }

        if (!ctype_digit($bankCode) || !ctype_digit($accountNumber)) {
            $this->logger->error("Bank code or account number contains invalid characters");
            return "";
        }

        // Handle special case for Czech Republic
        if ($countryCode === 'CZ') {
            return $this->generateForCzech($bankCode, $accountNumber, $bankPrefix);
        }

        // General handling for other countries
        return $this->generateForOtherCountries($countryCode, $bankCode, $accountNumber);
    }

    /**
     * Validates the country code.
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code.
     * @return bool True if the country code is valid; false otherwise.
     */
    protected function isValidCountryCode(string $countryCode): bool
    {
        return isset(self::$ibanLengths[$countryCode]) && preg_match('/^[A-Z]{2}$/', $countryCode);
    }

    /**
     * Generates an IBAN for the Czech Republic.
     *
     * @param string $bankCode Bank code (numeric).
     * @param string $accountNumber Account number (numeric).
     * @param string $bankPrefix Optional bank prefix.
     * @return string The generated IBAN or an empty string if invalid inputs are provided.
     */
    protected function generateForCzech(string $bankCode, string $accountNumber, string $bankPrefix): string
    {
        $validator = new CzechBankAccountValidator($this->logger, "$bankPrefix-$accountNumber/$bankCode");

        if ($validator->isValid()) {
            return $validator->getIban();
        }

        $this->logger->error("Invalid Czech account: $bankCode, $accountNumber");
        return "";
    }

    /**
     * Generates an IBAN for countries other than the Czech Republic.
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code.
     * @param string $bankCode Bank code (numeric).
     * @param string $accountNumber Account number (numeric).
     * @return string The generated IBAN.
     */
    protected function generateForOtherCountries(string $countryCode, string $bankCode, string $accountNumber): string
    {
        $ibanLength = self::$ibanLengths[$countryCode];
        $bbanLength = $ibanLength - 4; // Subtract 4 characters for country code and check digits.

        // Ensure BBAN has the correct length
        $bban = str_pad($bankCode, 8, '0', STR_PAD_LEFT) // 8 characters for bank code.
            . str_pad($accountNumber, $bbanLength - 8, '0', STR_PAD_LEFT); // Remaining characters for account number.

        // Calculate check digits
        $checkDigits = $this->calculateCheckDigits($countryCode, $bban);

        // Return full IBAN
        return $countryCode . str_pad((string) $checkDigits, 2, '0', STR_PAD_LEFT) . $bban;
    }

    /**
     * Calculates the IBAN check digits.
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code.
     * @param string $bban Basic Bank Account Number (BBAN).
     * @return int The calculated check digits.
     */
    protected function calculateCheckDigits(string $countryCode, string $bban): int
    {
        $numericCountryCode = '';

        foreach (str_split($countryCode) as $char) {
            $numericCountryCode .= ord($char) - 55; // A=10, B=11, ...
        }

        $numericIBAN = $bban . $numericCountryCode . '00';

        return (98 - (int) bcmod((string)$numericIBAN, '97'));
    }

}