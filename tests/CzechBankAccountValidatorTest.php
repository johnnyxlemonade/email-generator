<?php


namespace Lemonade\EmailGenerator\Tests\Validators\IBAN;

use Lemonade\EmailGenerator\Validators\IBAN\CzechBankAccountValidator;
use PHPUnit\Framework\TestCase;

class CzechBankAccountValidatorTest extends TestCase
{
    public function testValidAccount(): void
    {
        $validator = new CzechBankAccountValidator("705-77628031/0710");
        $this->assertTrue($validator->isValid());
        $this->assertEquals("CZ2607100007050077628031", $validator->generateIban());
    }

    public function testInvalidAccount(): void
    {
        $validator = new CzechBankAccountValidator("1234567890123456/1234");
        $this->assertFalse($validator->isValid());
        $this->assertEquals("", $validator->generateIban());
    }

    public function testGetBic(): void
    {
        $validator = new CzechBankAccountValidator("705-77628031/0710");
        $this->assertEquals("CNBACZPP", $validator->getBic());
    }

    public function testGetName(): void
    {
        $validator = new CzechBankAccountValidator("705-77628031/0710");
        $this->assertEquals("Česká národní banka", $validator->getName("0710"));
    }

    public function testInvalidBankCode(): void
    {
        $validator = new CzechBankAccountValidator("705-77628031/0710123");
        $this->assertEquals("", $validator->getBic());
        $this->assertEquals("", $validator->getName("0710123"));
    }
}