<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{AddressBlock, Columns, Image, Signature, Text};
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\ValueObject\{Address, Contact, ImageSource, Translation};
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies contact composition normalization, expansion and validation
 */
final class ContactCompositionTest extends TestCase
{
    public function testSignatureNormalizesOptionalValuesAndChoosesLayout(): void
    {
        $contact = new Contact('Alex', 'Designer', 'Northstar', email: '', phone: '  ');
        $plain = new Signature($contact);
        self::assertNull($contact->email());
        self::assertNull($contact->phone());
        self::assertSame($contact, $plain->contact());
        self::assertNull($plain->image());
        self::assertInstanceOf(Text::class, iterator_to_array($plain->components())[0]);

        $withImage = new Signature(
            contact: $contact,
            image: new ImageSource('https://example.com/alex.png', 'Alex'),
        );
        $columns = iterator_to_array($withImage->components())[0];
        self::assertInstanceOf(Columns::class, $columns);
        self::assertSame(112, $columns->firstWidth());
        self::assertSame(72, $columns->mobileFirstWidth());
        $image = $columns->first()[0];
        self::assertInstanceOf(Image::class, $image);
        self::assertSame(112, $image->width());
    }

    public function testAddressNormalizesBlankOptionalValuesWithoutDroppingData(): void
    {
        $data = new Address(
            'Northstar GmbH',
            'Friedrichstraße 42',
            'Berlin',
            '10117',
            'Germany',
            companyId: 'HRB 123456',
            email: ' ',
        );
        $address = new AddressBlock($data);

        self::assertSame('HRB 123456', $data->companyId());
        self::assertNull($data->email());
        self::assertNull($data->taxId());
        self::assertNull($data->phone());
        self::assertSame($data, $address->address());
    }

    public function testContactKeepsTranslationsAndOmitsBlankIdentityDetails(): void
    {
        $name = new Translation('contact.name');
        $organization = new Translation('contact.organization');
        $contact = new Contact($name, position: ' ', organization: $organization);

        self::assertSame($name, $contact->name());
        self::assertNull($contact->position());
        self::assertSame($organization, $contact->organization());
    }

    /**
     * @param Closure(): object $construct
     */
    #[DataProvider('invalidInput')]
    public function testInvalidInputIsRejected(Closure $construct, ErrorCode $errorCode): void
    {
        try {
            $construct();
            self::fail('Invalid contact composition input was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($errorCode, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{Closure(): object, ErrorCode}>
     */
    public static function invalidInput(): iterable
    {
        yield 'contact name' => [static fn(): Contact => new Contact("Bad\0"), ErrorCode::TextInvalid];
        yield 'contact detail' => [static fn(): Contact => new Contact('Name', email: "Bad\0"), ErrorCode::TextInvalid];
        yield 'image URL' => [static fn(): ImageSource => new ImageSource('cid:portrait'), ErrorCode::UrlInvalid];
        yield 'image alt' => [static fn(): ImageSource => new ImageSource('https://example.com/image.png', "Bad\0"), ErrorCode::TextInvalid];
        yield 'address text' => [static fn(): Address => new Address('Name', 'Street', 'City', '12345', "Bad\0"), ErrorCode::TextInvalid];
        yield 'address contact' => [static fn(): Address => new Address('Name', 'Street', 'City', '12345', 'Country', phone: "Bad\0"), ErrorCode::TextInvalid];
    }
}
