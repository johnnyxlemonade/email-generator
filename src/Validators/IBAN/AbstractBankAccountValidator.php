<?php

namespace Lemonade\EmailGenerator\Validators\IBAN;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Psr\Log\LoggerInterface;

abstract class AbstractBankAccountValidator implements BankAccountValidatorInterface
{

    /**
     * Cached bank data loaded from the JSON file.
     *
     * @var array<string, array{name: string, swift: string}>|null
     */
    private ?array $cachedBanks = null;

    /**
     * Indicates whether the account is valid.
     *
     * @var bool
     */
    protected bool $accountIsValid = false;

    /**
     * The prefix part of the account number.
     *
     * @var string|null
     */
    protected ?string $accountPrefix = null;

    /**
     * The main part of the account number.
     *
     * @var string|null
     */
    protected ?string $accountNumber = null;

    /**
     * The bank code associated with the account.
     *
     * @var string|null
     */
    protected ?string $accountCode = null;

    /**
     * The full account number in the format prefix-number/code.
     *
     * @var string|null
     */
    protected ?string $accountFull = null;

    /**
     * Weight array for validating the account prefix.
     *
     * @var array<int>
     */
    protected array $prefixWeight = [10, 5, 8, 4, 2, 1];

    /**
     * Weight array for validating the account number.
     *
     * @var array<int>
     */
    protected array $numberWeight = [6, 3, 7, 9, 10, 5, 8, 4, 2, 1];

    /**
     * Constructor for the bank account validator.
     *
     * @param LoggerInterface $logger A logger instance for recording warnings and errors.
     * @param string $number The account number to validate and initialize.
     */
    public function __construct(protected readonly LoggerInterface $logger, string $number)
    {

        $this->logger->info("Initializing BankAccountValidator for account number: {$number}");

        $this->initializeClass($number);
        $this->initializeAssertation();
    }

    /**
     * Checks if the stored account is valid.
     *
     * @return bool True if the account is valid, false otherwise.
     */
    public function isValid(): bool
    {
        return $this->accountIsValid;
    }

    /**
     * Retrieves a list of banks and their details.
     *
     * @return array<string, array{name: string, swift: string}> An array of bank details keyed by bank code.
     */
    public function getBanks(): array
    {
        return $this->readBanksFromFile();
    }

    /**
     * Retrieves the name of the bank associated with the account's bank code.
     *
     * @return string The bank name, or an empty string if not found.
     */
    public function getBankName(): string
    {

        $code  = $this->getAccountCode();
        $banks = $this->getBanks();


        if (!isset($banks[$code])) {

            $this->logger->warning("Bank not found for code: {$code}");

            return '';
        }

        return $banks[$code]['name'];
    }

    /**
     * Retrieves the SWIFT code of the bank associated with the account's bank code.
     *
     * @return string The SWIFT code, or an empty string if not found.
     */
    public function getBankSwift(): string
    {

        $code  = $this->getAccountCode();
        $banks = $this->getBanks();

        if (!isset($banks[$code])) {

            $this->logger->warning("Bank not found for code: {$code}");
            return '';
        }

        return str_replace(" ", "", $banks[$code]['swift']);
    }

    /**
     * Returns the account prefix.
     *
     * @return string|null The account prefix, or null if not set.
     */
    public function getAccountPrefix(): ?string
    {
        return $this->accountPrefix;
    }

    /**
     * Returns the account number.
     *
     * @return string|null The account number, or null if not set.
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    /**
     * Returns the bank code associated with the account.
     *
     * @return string|null The bank code, or null if not set.
     */
    public function getAccountCode(): ?string
    {
        return $this->accountCode;
    }

    /**
     * Constructs the BBAN (Basic Bank Account Number) from the account data.
     *
     * @return string The BBAN string.
     */
    public function getBban(): string
    {

        return $this->accountCode . $this->accountPrefix . $this->accountNumber;
    }

    /**
     * Constructs the IBAN (International Bank Account Number) from the account data.
     *
     * @return string The IBAN string, or an empty string if the account is invalid.
     */
    public function getIban(): string
    {
        if ($this->isValid() === false) {
            return '';
        }

        $bban = $this->getBban();
        $countryCode = $this->getCountryCode();

        $checkDigits = $this->generateIbanVerificationCode($countryCode, $bban);

        return $countryCode . $checkDigits . $bban;
    }

    /**
     * Validates the account data using weights.
     *
     * @return void
     */
    protected function initializeAssertation(): void
    {
        $prefixWeight = $this->getPrefixWeight();
        $numberWeight = $this->getNumberWeight();


        if ((string) $this->accountNumber === "" || strlen((string)$this->accountNumber) < 2) {
            $this->logger->warning("Invalid account number: {$this->accountNumber}");
            $this->accountIsValid = false;
            return;
        }

        if ((string) $this->accountCode === "" || strlen((string)$this->accountCode) !== 4) {
            $this->logger->warning("Invalid bank code: {$this->accountCode}");
            $this->accountIsValid = false;
            return;
        }

        $this->logger->info("Validating account data.");

        $this->accountIsValid = $this->validateControlSum($this->accountNumber, $numberWeight) && $this->validateControlSum($this->accountPrefix, $prefixWeight);
    }

    /**
     * Generates the IBAN verification code (check digits).
     *
     * @param string $countryCode The ISO country code.
     * @param string $bban The BBAN string.
     * @return string The IBAN check digits.
     */
    protected function generateIbanVerificationCode(string $countryCode, string $bban): string
    {
        // Kód země převedený na numerickou reprezentaci
        $numericCountryCode = $this->convertCountryCode($countryCode);

        // Připojte BBAN, kód země a "00"
        $reformatted = $bban . $numericCountryCode . '00';

        // Vypočítejte modulo 97
        $remainder = $this->calculateMod($reformatted, '97');

        // Odečtěte od 98 a zarovnejte na dvě číslice
        $checkDigits = str_pad((string)(98 - (int) $remainder), 2, '0', STR_PAD_LEFT);

        return $checkDigits;
    }

    /**
     * Converts a country code to its numeric equivalent for IBAN calculation.
     *
     * @param string $countryCode The ISO country code.
     * @return string The numeric equivalent of the country code.
     */
    protected function convertCountryCode(string $countryCode): string
    {
        $converted = '';
        foreach (str_split($countryCode) as $char) {
            $converted .= ord($char) - 55;
        }

        return $converted;
    }

    /**
     * Pads a string to the left with zeros to achieve a specific length.
     *
     * @param string|null $value The string to pad.
     * @param int $length The desired length.
     * @return string The padded string.
     */
    protected function padLeft(?string $value, int $length): string
    {
        return str_pad($value ?? '', $length, '0', STR_PAD_LEFT);
    }

    /**
     * Validates the control sum of a bank account number using predefined weights.
     *
     * @param string|null $number The number to validate.
     * @param array<int> $weights The weights used for the validation.
     * @return bool True if the control sum is valid, false otherwise.
     */
    protected function validateControlSum(?string $number, array $weights): bool
    {
        // Pokud je prefix prázdný nebo sestává pouze z nul, validaci přeskoč
        if ($number === null || $number === '' || ltrim($number, '0') === '') {
            $this->logger->info("Control sum validation skipped for empty or zero-only prefix.");
            return true;
        }

        if (strlen($number) !== count($weights)) {
            $this->logger->warning("Control sum validation failed due to incorrect length. Number: {$number}");
            return false;
        }

        $sum = 0;

        foreach ($weights as $index => $weight) {
            $digit = (int)($number[$index] ?? 0);
            $sum += $digit * $weight;

            $this->logger->debug(sprintf(
                "Index: %d, Digit: %d, Weight: %d, Partial Sum: %d",
                $index,
                $digit,
                $weight,
                $sum
            ));
        }

        $modResult = $this->calculateMod((string)$sum, '11');

        $this->logger->info("Final Sum: {$sum}, Modulo 11 Result: {$modResult}");

        return $modResult === '0';
    }

    /**
     * Returns the weight array for validating the account prefix.
     *
     * @return array<int>
     */
    protected function getPrefixWeight(): array
    {
        return $this->prefixWeight;
    }

    /**
     * Returns the weight array for validating the account number.
     *
     * @return array<int>
     */
    protected function getNumberWeight(): array
    {
        return $this->numberWeight;
    }

    /**
     * Calculates the remainder of a division (modulus) for large numbers.
     *
     * Uses the built-in `bcmod` function if available; otherwise, falls back to a custom implementation.
     *
     * @param string $number The dividend as a string (to handle large numbers).
     * @param string $modulo The divisor as a string.
     * @return string The remainder of the division.
     */
    private function calculateMod(string $number, string $modulo): string
    {

        $this->logger->debug("Calculating modulo. Number: {$number}, Modulo: {$modulo}");

        if (function_exists('bcmod')) {
            $result = bcmod($number, $modulo);
            $this->logger->debug("BCMOD result: {$result}");
            return $result;
        }

        return $this->alternativeBcmod($number, $modulo);
    }

    /**
     * Custom implementation of modulus operation for large numbers.
     *
     * This method is used when `bcmod` is not available and works by iteratively calculating
     * the remainder using modulo arithmetic.
     *
     * @param string $number The dividend as a string (to handle large numbers).
     * @param string $modulo The divisor as a string.
     * @return string The remainder of the division.
     */
    private function alternativeBcmod(string $number, string $modulo): string
    {
        $remainder = 0;

        // Iterate over each digit of the number
        foreach (str_split($number) as $digit) {
            $remainder = ($remainder * 10 + (int)$digit) % (int)$modulo;
            $this->logger->debug("Processing digit: {$digit}, Current Remainder: {$remainder}");
        }

        // Return the remainder as a string to match bcmod's behavior
        $this->logger->debug("Final Remainder: {$remainder}");

        return (string)$remainder;
    }

    /**
     * Reads and caches bank data from the appropriate JSON file.
     *
     * @return array<string, array{name: string, swift: string}>
     */
    private function readBanksFromFile(): array
    {
        $sourceFile = __DIR__ . '/storage/' . $this->getCountryCode() . '.json';
        $cacheFile = $this->getCacheFilePath($sourceFile);

        if ($this->cachedBanks !== null) {
            return $this->cachedBanks;
        }

        if ($this->isCacheValid($sourceFile)) {
            $cacheContent = file_get_contents($cacheFile);

            if ($cacheContent !== false) {
                $cachedData = @unserialize($cacheContent);

                if (is_array($cachedData)) {
                    /** @var array<string, array{name: string, swift: string}> $cachedData */
                    $this->cachedBanks = $cachedData;
                    return $this->cachedBanks;
                }
            }
        }

        if (!file_exists($sourceFile)) {
            $this->logger->warning("Bank data file not found: {$sourceFile}");
            return [];
        }

        $data = file_get_contents($sourceFile);

        if ($data === false || trim($data) === '') {
            $this->logger->warning("Bank data file is empty: {$sourceFile}");
            return [];
        }

        $banks = json_decode($data, true, 512, JSON_BIGINT_AS_STRING);

        if (!is_array($banks)) {
            $this->logger->error("Error decoding or invalid format in JSON file: {$sourceFile}");
            return [];
        }

        file_put_contents($cacheFile, serialize($banks));
        /** @var array<string, array{name: string, swift: string}> $banks */
        $this->cachedBanks = $banks;

        return $this->cachedBanks;
    }

    /**
     * Generates the cache file path based on the source file path.
     *
     * This method creates a unique file path for cached data by hashing the source file path.
     *
     * @param string $sourceFile The path to the source file.
     * @return string The path to the cache file.
     */
    private function getCacheFilePath(string $sourceFile): string
    {
        $hash = md5($sourceFile);
        return sys_get_temp_dir() . "/bank_data_{$hash}.cache";
    }

    /**
     * Checks whether the cache file is valid.
     *
     * The cache is considered valid if the cache file exists and its modification time
     * is newer than or equal to the source file's modification time.
     *
     * @param string $sourceFile The path to the source file.
     * @return bool True if the cache is valid, false otherwise.
     */
    private function isCacheValid(string $sourceFile): bool
    {
        $cacheFile = $this->getCacheFilePath($sourceFile);
        return file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($sourceFile);
    }

    /**
     * Abstract method to initialize the class with the provided account number.
     *
     * Each subclass must implement this method to parse and initialize the account number
     * according to the specific country's format and rules.
     *
     * @param string $accountNumber The account number to initialize.
     * @return void
     */
    abstract protected function initializeClass(string $accountNumber): void;

}