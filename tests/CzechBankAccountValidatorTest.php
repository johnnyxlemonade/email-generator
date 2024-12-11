<?php


namespace Lemonade\EmailGenerator\Tests\Validators\IBAN;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Lemonade\EmailGenerator\Validators\IBAN\CzechBankAccountValidator;

class CzechBankAccountValidatorTest extends TestCase
{
    public function testConstructorInitializesValidator(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $accountNumber = '705-77628031/0710';

        $validator = new CzechBankAccountValidator($logger, $accountNumber);

        $this->assertTrue($validator->isValid());
        $this->assertSame('000705', $validator->getAccountPrefix()); // Zarovnaný prefix
        $this->assertSame('0077628031', $validator->getAccountNumber()); // Zarovnané číslo účtu
        $this->assertSame('0710', $validator->getAccountCode());
    }

    public function testGetIbanReturnsCorrectValue(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $accountNumber = '705-77628031/0710';

        $validator = new CzechBankAccountValidator($logger, $accountNumber);

        $expectedIban = 'CZ2607100007050077628031';
        $this->assertSame($expectedIban, $validator->getIban());
    }

    public function testValidAccount(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $accountNumber = '705-77628031/0710';

        $validator = new CzechBankAccountValidator($logger, $accountNumber);

        $this->assertTrue($validator->isValid());
        $this->assertNull($validator->getValidationError());
        $expectedIban = 'CZ2607100007050077628031';
        $this->assertSame($expectedIban, $validator->getIban());
    }

    public function testInvalidAccountNumbers(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $invalidAccounts = [
            '75-1/0710',
            '705-12345678901/0710',
            '705-abcdef/0710',
            '705-77628031/abcd',
            '705-77628031/',
            '/0710',
            '',
        ];

        foreach ($invalidAccounts as $accountNumber) {
            $validator = new CzechBankAccountValidator($logger, $accountNumber);

            // Validace by měla vracet false
            $this->assertFalse(
                $validator->isValid(),
                "Account {$accountNumber} should be invalid."
            );

            // IBAN by měl být prázdný
            $this->assertSame(
                '',
                $validator->getIban(),
                "IBAN for {$accountNumber} should be empty."
            );

            // Prefix, číslo účtu a kód banky by měly být null nebo prázdné
            $this->assertNull($validator->getAccountPrefix(), "Prefix for {$accountNumber} should be null.");
            $this->assertNull($validator->getAccountNumber(), "Account number for {$accountNumber} should be null.");
            $this->assertNull($validator->getAccountCode(), "Bank code for {$accountNumber} should be null.");
        }
    }


    public function testMissingBankData(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $accountNumber = '705-77628031/9999'; // Bank code 9999 není v JSON

        $validator = new CzechBankAccountValidator($logger, $accountNumber);

        $this->assertTrue($validator->isValid());
        $this->assertSame('', $validator->getBankName());
        $this->assertSame('', $validator->getBankSwift());
    }

    public function testInvalidAccountNumberCauseIBAN(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $invalidAccounts = [
            '12/0001', // Invalid due to control sum
        ];

        foreach ($invalidAccounts as $accountNumber) {
            $validator = new CzechBankAccountValidator($logger, $accountNumber);
            $this->assertFalse($validator->isValid(), "Account {$accountNumber} should be invalid.");
            $this->assertSame('', $validator->getIban(), "IBAN for {$accountNumber} should be empty.");
        }
    }

}