<?php

namespace Lemonade\EmailGenerator\Validators\IBAN;

/**
 * Class CzechBankAccountValidator
 * Provides validation and manipulation for bank accounts.
 */
class CzechBankAccountValidator extends AbstractBankAccountValidator
{

    /**
     * Initializes the bank account attributes from the given account number.
     *
     * Parses the account number in the format "prefix-number/code" and sets the
     * respective attributes such as bank prefix, account number, and bank code.
     *
     * @param string $accountNumber The bank account number to parse and initialize.
     * @return void
     */
    protected function initialize(string $accountNumber): void
    {

        if (preg_match('/^((\d{0,6})-)?(\d{2,10})\/(\d{4})$/', $accountNumber, $matchList) === 1) {
            $this->bankPrefix = $this->padLeft($matchList[2], 6);
            $this->bankNumber = $this->padLeft($matchList[3], 10);
            $this->bankCode = $this->padLeft($matchList[4], 4);
            $this->bankPrefixInt = (int) $matchList[2];
            $this->bankNumberInt = (int) $matchList[3];
            $this->bankAccount = $this->bankPrefix . '-' . $this->bankNumber . '/' . $this->bankCode;
        }
    }

    /**
     * Validates the bank account using control sums.
     *
     * This method checks the validity of the account prefix and account number
     * using predefined weight arrays for control sum calculations.
     *
     * @return void
     */
    protected function validateAccount(): void
    {
        $prefixWeight = [10, 5, 8, 4, 2, 1];
        $numberWeight = [6, 3, 7, 9, 10, 5, 8, 4, 2, 1];

        $this->ibanValid = $this->validateControlSum($this->bankPrefix, $prefixWeight) &&
            $this->validateControlSum($this->bankNumber, $numberWeight);
    }

    /**
     * Generates the IBAN for the Czech bank account.
     *
     * This method calculates the IBAN using the bank code, account prefix, and account number.
     * If the account is invalid, it returns an empty string instead of throwing an exception.
     *
     * @return string The generated IBAN in the format "CZXXYYYYYYYYYYYYYYYY" or an empty string if invalid.
     */
    public function generateIban(): string
    {

        if (!$this->ibanValid) {

            return '';
        }

        return sprintf(
            'CZ%s%s%s%s',
            $this->generateIbanVerificationCode(),
            $this->bankCode,
            $this->bankPrefix,
            $this->bankNumber
        );
    }

    /**
     * Returns a list of banks and their details.
     *
     * This method provides an associative array where each key is a bank code,
     * and the value is an array containing the bank's name and BIC/SWIFT code.
     *
     * @return array<string, array{name: string, bic: string}> An array of banks with their codes, names, and BIC/SWIFT codes.
     */
    public function getBanks(): array
    {

        /** @var array<string, array{name: string, bic: string}> $data */
        $data = [
            '0100' => ['name' => 'Komerční banka, a.s.', 'bic' => 'KOMB CZ PP'],
            '0300' => ['name' => 'Československá obchodní banka, a.s.', 'bic' => 'CEKO CZ PP'],
            '0600' => ['name' => 'MONETA Money Bank, a.s.', 'bic' => 'AGBA CZ PP'],
            '0710' => ['name' => 'Česká národní banka', 'bic' => 'CNBA CZ PP'],
            '0800' => ['name' => 'Česká spořitelna, a.s.', 'bic' => 'GIBA CZ PX'],
            '2010' => ['name' => 'Fio banka, a.s.', 'bic' => 'FIOB CZ PP'],
            '2020' => ['name' => 'MUFG Bank (Europe) N.V. Prague Branch', 'bic' => 'BOTK CZ PP'],
            '2030' => ['name' => 'Československé úvěrní družstvo', 'bic' => ''],
            '2060' => ['name' => 'Citfin, spořitelní družstvo', 'bic' => 'CITF CZ PP'],
            '2070' => ['name' => 'Moravský Peněžní Ústav – spořitelní družstvo', 'bic' => 'MPUB CZ PP'],
            '2100' => ['name' => 'Hypoteční banka, a.s.', 'bic' => ''],
            '2200' => ['name' => 'Peněžní dům, spořitelní družstvo', 'bic' => ''],
            '2220' => ['name' => 'Artesa, spořitelní družstvo', 'bic' => 'ARTT CZ PP'],
            '2240' => ['name' => 'Poštová banka, a.s., pobočka Česká republika', 'bic' => 'POBN CZ PP'],
            '2250' => ['name' => 'Banka CREDITAS a.s.', 'bic' => 'CTAS CZ 22'],
            '2260' => ['name' => 'NEY spořitelní družstvo', 'bic' => ''],
            '2275' => ['name' => 'Podnikatelská družstevní záložna', 'bic' => ''],
            '2600' => ['name' => 'Citibank Europe plc, organizační složka', 'bic' => 'CITI CZ PX'],
            '2700' => ['name' => 'UniCredit Bank Czech Republic and Slovakia, a.s.', 'bic' => 'BACX CZ PP'],
            '3030' => ['name' => 'Air Bank a.s.', 'bic' => 'AIRA CZ PP'],
            '3050' => ['name' => 'BNP Paribas Personal Finance SA, odštěpný závod', 'bic' => 'BPPF CZ P1'],
            '3060' => ['name' => 'PKO BP S.A., Czech Branch', 'bic' => 'BPKO CZ PP'],
            '3500' => ['name' => 'ING Bank N.V.', 'bic' => 'INGB CZ PP'],
            '4000' => ['name' => 'Expobank CZ a.s.', 'bic' => 'EXPN CZ PP'],
            '4300' => ['name' => 'Českomoravská záruční a rozvojová banka, a.s.', 'bic' => 'CMZR CZ P1'],
            '5500' => ['name' => 'Raiffeisenbank a.s.', 'bic' => 'RZBC CZ PP'],
            '5800' => ['name' => 'J&T BANKA, a.s.', 'bic' => 'JTBP CZ PP'],
            '6000' => ['name' => 'PPF banka a.s.', 'bic' => 'PMBP CZ PP'],
            '6100' => ['name' => 'Equa bank a.s.', 'bic' => 'EQBK CZ PP'],
            '6200' => ['name' => 'COMMERZBANK Aktiengesellschaft, pobočka Praha', 'bic' => 'COBA CZ PX'],
            '6210' => ['name' => 'mBank S.A., organizační složka', 'bic' => 'BREX CZ PP'],
            '6300' => ['name' => 'BNP Paribas S.A., pobočka Česká republika', 'bic' => 'GEBA CZ PP'],
            '6700' => ['name' => 'Všeobecná úverová banka a.s., pobočka Praha', 'bic' => 'SUBA CZ PP'],
            '6800' => ['name' => 'Sberbank CZ, a.s.', 'bic' => 'VBOE CZ 2X'],
            '7910' => ['name' => 'Deutsche Bank Aktiengesellschaft Filiale Prag, organizační složka', 'bic' => 'DEUT CZ PX'],
            '7940' => ['name' => 'Waldviertler Sparkasse Bank AG', 'bic' => 'SPWT CZ 21'],
            '7950' => ['name' => 'Raiffeisen stavební spořitelna a.s.', 'bic' => ''],
            '7960' => ['name' => 'Českomoravská stavební spořitelna, a.s.', 'bic' => ''],
            '7970' => ['name' => 'Wüstenrot - stavební spořitelna a.s.', 'bic' => ''],
            '7980' => ['name' => 'Wüstenrot hypoteční banka a.s.', 'bic' => ''],
            '7990' => ['name' => 'Modrá pyramida stavební spořitelna, a.s.', 'bic' => ''],
            '8030' => ['name' => 'Volksbank Raiffeisenbank Nordoberpfalz eG pobočka Cheb', 'bic' => 'GENO CZ 21'],
            '8040' => ['name' => 'Oberbank AG pobočka Česká republika', 'bic' => 'OBKL CZ 2X'],
            '8060' => ['name' => 'Stavební spořitelna České spořitelny, a.s.', 'bic' => ''],
            '8090' => ['name' => 'Česká exportní banka, a.s.', 'bic' => 'CZEE CZ PP'],
            '8150' => ['name' => 'HSBC Bank plc - pobočka Praha', 'bic' => 'MIDL CZ PP'],
            '8200' => ['name' => 'PRIVAT BANK der Raiffeisenlandesbank Oberösterreich Aktiengesellschaft, pobočka Česká republika', 'bic' => ''],
            '8215' => ['name' => 'ALTERNATIVE PAYMENT SOLUTIONS, s.r.o.', 'bic' => ''],
            '8220' => ['name' => 'Payment Execution s.r.o.', 'bic' => 'PAER CZ P1'],
            '8230' => ['name' => 'EEPAYS s. r. o.', 'bic' => 'EEPS CZ PP'],
            '8240' => ['name' => 'Družstevní záložna Kredit', 'bic' => ''],
            '8250' => ['name' => 'Bank of China (Hungary) Close Ltd. Prague branch, odštěpný závod', 'bic' => 'BKCH CZ PP'],
            '8260' => ['name' => 'PAYMASTER a.s.', 'bic' => ''],
            '8265' => ['name' => 'Industrial and Commercial Bank of China Limited Prague Branch, odštěpný závod', 'bic' => 'ICBK CZ PP'],
            '8270' => ['name' => 'Fairplay Pay s.r.o.', 'bic' => ''],
            '8280' => ['name' => 'B-Efekt a.s.', 'bic' => 'BEFK CZ P1'],
            '8290' => ['name' => 'EUROPAY s.r.o.', 'bic' => 'ERSO CZ PP'],
        ];

        return $data;
    }

    /**
     * Generuje verifikační kód pro IBAN.
     *
     * @return string Verifikační kód.
     */
    private function generateIbanVerificationCode(): string
    {
        $format = $this->bankCode . $this->bankPrefix . $this->bankNumber . '123500';

        for ($i = 0; $i < 100; $i++) {
            $vc = str_pad((string)$i, 2, '0', STR_PAD_LEFT);
            if ($this->calculateMod($format . $vc, '97') === '1') {
                return $vc;
            }
        }

        return '00';
    }

    /**
     * Validates the control checksum of a bank account number.
     *
     * This method calculates the control checksum using the provided weights and
     * checks whether the number is valid.
     *
     * @param string|null $number The account number to validate.
     * @param array<int> $weights The weights used for the checksum calculation.
     * @return bool True if the checksum is valid, false otherwise.
     */
    private function validateControlSum(?string $number, array $weights): bool
    {
        if ($number === null) {
            return false;
        }

        $sum = 0;

        foreach ($weights as $key => $weight) {
            if (!isset($number[$key])) {
                return false;
            }
            $sum += (int)$number[$key] * $weight;
        }

        return $this->calculateMod((string)$sum, "11") === "0";
    }

    /**
     * Returns the BIC/SWIFT code of the bank based on the bank code.
     *
     * This method retrieves the BIC/SWIFT code for the bank associated with the current bank code.
     *
     * @return string The BIC/SWIFT code of the bank, or an empty string if not found.
     */
    public function getBic(): string
    {

        $code  = $this->getCode();
        $banks = $this->getBanks();

        return str_replace(" ", "", $banks[$code]['bic'] ?? '');
    }

    /**
     * Returns the name of the bank based on the bank code.
     *
     * This method retrieves the bank's name corresponding to the given bank code.
     *
     * @param string $code The bank code.
     * @return string The name of the bank, or an empty string if not found.
     */
    public function getName(string $code): string
    {

        $banks = $this->getBanks();

        return $banks[$code]['name'] ?? '';
    }
    /**
     * Returns the full bank account number.
     *
     * @return string|null The full account number, or null if not set.
     */
    public function getAccount(): ?string
    {
        return $this->bankAccount;
    }

    /**
     * Returns the account prefix as a string.
     *
     * @return string|null The account prefix, or null if not set.
     */
    public function getPrefix(): ?string
    {
        return $this->bankPrefix;
    }

    /**
     * Returns the account prefix as an integer.
     *
     * @return int|null The account prefix as an integer, or null if not set.
     */
    public function getPrefixInt(): ?int
    {
        return $this->bankPrefixInt;
    }

    /**
     * Returns the account number as a string.
     *
     * @return string|null The account number, or null if not set.
     */
    public function getNumber(): ?string
    {
        return $this->bankNumber;
    }

    /**
     * Returns the account number as an integer.
     *
     * @return int|null The account number as an integer, or null if not set.
     */
    public function getNumberInt(): ?int
    {
        return $this->bankNumberInt;
    }

    /**
     * Returns the bank code.
     *
     * @return string|null The bank code, or null if not set.
     */
    public function getCode(): ?string
    {
        return $this->bankCode;
    }

    /**
     * Calculates the remainder of a division (alternative to bcmod).
     *
     * If the bcmod function is available, it uses the native implementation.
     * Otherwise, a custom implementation is used.
     *
     * @param string $number The dividend as a string.
     * @param string $modulo The divisor as a string.
     * @return string The remainder of the division as a string.
     */
    private function calculateMod(string $number, string $modulo): string
    {
        if (function_exists('bcmod')) {
            return bcmod($number, $modulo);
        }

        return $this->alternativeBcmod($number, $modulo);
    }

    /**
     * Custom implementation of bcmod for numbers larger than PHP_INT_MAX.
     *
     * This method iterates over each digit of the number and calculates the remainder
     * using modulo operation, suitable for large numbers represented as strings.
     *
     * @param string $number The dividend as a string.
     * @param string $modulo The divisor as a string.
     * @return string The remainder of the division as a string.
     */
    private function alternativeBcmod(string $number, string $modulo): string
    {
        $remainder = 0;

        // Iterate over each digit in the number
        foreach (str_split($number) as $digit) {
            $remainder = ($remainder * 10 + (int)$digit) % (int)$modulo;
        }

        return (string)$remainder;
    }


}
