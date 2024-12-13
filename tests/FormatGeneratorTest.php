<?php

namespace Lemonade\EmailGenerator\Tests\Validators\IBAN;

use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\Validators\IBAN\FormatGenerator;
use Lemonade\EmailGenerator\Validators\IBAN\CzechBankAccountValidator;
use Psr\Log\LoggerInterface;

class FormatGeneratorTest extends TestCase
{
    private LoggerInterface $mockLogger;
    private FormatGenerator $formatGenerator;

    protected function setUp(): void
    {
        // Create a mock logger
        $this->mockLogger = $this->createMock(LoggerInterface::class);

        // Initialize the FormatGenerator instance
        $this->formatGenerator = new FormatGenerator($this->mockLogger);
    }

    public function testGenerateCode_ValidInputs_ReturnsCorrectIban(): void
    {
        $countryCode = 'DE';
        $bankCode = '12345678';
        $accountNumber = '1234567890';

        $iban = $this->formatGenerator->generateCode($countryCode, $bankCode, $accountNumber);

        $expectedLength = 22; // IBAN length for Germany
        $this->assertNotEmpty($iban, "IBAN should not be empty");
        $this->assertEquals($expectedLength, strlen($iban), "IBAN length should match expected length");
    }

    public function testGenerateCode_InvalidCountryCode_ReturnsEmptyString(): void
    {
        $countryCode = 'ZZ'; // Invalid country code
        $bankCode = '12345678';
        $accountNumber = '1234567890';

        $this->mockLogger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains("Unsupported or invalid country code"));

        $iban = $this->formatGenerator->generateCode($countryCode, $bankCode, $accountNumber);

        $this->assertEmpty($iban, "IBAN should be empty for invalid country code");
    }

    public function testGenerateCode_InvalidBankCode_ReturnsEmptyString(): void
    {
        $countryCode = 'DE';
        $bankCode = 'INVALID';
        $accountNumber = '1234567890';

        $this->mockLogger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains("Bank code or account number contains invalid characters"));

        $iban = $this->formatGenerator->generateCode($countryCode, $bankCode, $accountNumber);

        $this->assertEmpty($iban, "IBAN should be empty for invalid bank code");
    }

    public function testGenerateForCzech_ValidInputs_ReturnsCorrectIban(): void
    {
        $countryCode = 'CZ';
        $bankCode = '705';
        $accountNumber = '77628031';
        $bankPrefix = '7500';

        // Mock CzechBankAccountValidator
        $mockValidator = $this->getMockBuilder(CzechBankAccountValidator::class)
            ->setConstructorArgs([$this->mockLogger, "$bankPrefix-$accountNumber/$bankCode"])
            ->onlyMethods(['isValid', 'getIban'])
            ->getMock();

        // Nastavíme návratové hodnoty
        $mockValidator->method('isValid')->willReturn(true);
        $mockValidator->method('getIban')->willReturn('CZ7500705000077628031'); // Očekávaný IBAN pro dané údaje

        // Mockování loggeru, aby se neočekávalo žádné chybové volání
        $this->mockLogger
            ->expects($this->never()) // Logger::error nesmí být volán
            ->method('error');

        // Pro testování metody můžeme simulovat chování `generateForCzech`
        $class = new class($this->mockLogger) extends FormatGenerator {
            public CzechBankAccountValidator $mockValidator;

            protected function generateForCzech(string $bankCode, string $accountNumber, string $bankPrefix): string
            {
                return $this->mockValidator->getIban();
            }
        };

        $class->mockValidator = $mockValidator;

        // Testujeme IBAN generátor
        $iban = $class->generateCode($countryCode, $bankCode, $accountNumber, $bankPrefix);

        // Ověření výsledku
        $this->assertEquals('CZ7500705000077628031', $iban, "IBAN should match expected format for Czech Republic");
    }

    public function testCalculateCheckDigits_ReturnsCorrectDigits(): void
    {
        $countryCode = 'DE';
        $bban = '123456789012345678';

        // Přímé volání chráněné metody prostřednictvím rozšířené třídy
        $class = new class($this->mockLogger) extends FormatGenerator {
            public function calculateCheckDigitsProxy(string $countryCode, string $bban): int
            {
                return $this->calculateCheckDigits($countryCode, $bban);
            }
        };

        $checkDigits = $class->calculateCheckDigitsProxy($countryCode, $bban);

        // Ověření výsledků
        $this->assertIsInt($checkDigits, "Check digits should be an integer");
        $this->assertGreaterThanOrEqual(0, $checkDigits, "Check digits should be non-negative");
        $this->assertLessThanOrEqual(98, $checkDigits, "Check digits should be less than or equal to 98");
    }
}
