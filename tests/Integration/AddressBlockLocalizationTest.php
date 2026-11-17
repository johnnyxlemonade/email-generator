<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\EmailServiceProvider;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;
use Lemonade\Framework\Localization\TranslationResourceRegistry;

/**
 * Verifies package labels, fallback and application catalog precedence
 */
final class AddressBlockLocalizationTest extends EmailTestCase
{
    /**
     * Resolves Czech, English and fallback labels through the real provider bootstrap
     */
    public function testPackageCatalogsAndApplicationOverrideFollowFrameworkPriority(): void
    {
        $container = new Container();
        (new CliKernelFactory($container))->create(self::context())->bootstrap();

        $resources = $container->get(TranslationResourceRegistry::class);
        $resourceDirectory = realpath(dirname(__DIR__, 2) . '/resources/Language');
        self::assertNotFalse($resourceDirectory);
        self::assertContains($resourceDirectory, $resources->directories());
        self::assertContains(EmailServiceProvider::class, array_map(
            static fn($resource): ?string => $resource->owner,
            $resources->resources(),
        ));

        $address = new AddressBlock(new Address(
            name: 'Northstar s.r.o.',
            street: 'Pobřežní 1',
            city: 'Praha',
            postalCode: '186 00',
            country: 'Česká republika',
            companyId: '12345678',
            taxId: 'CZ12345678',
            email: 'office@example.com',
            phone: '+420 222 000 000',
        ));
        $emails = $container->get(EmailGenerator::class);

        $czech = $emails->create(locale: 'cs')->add($address)->render();
        self::assertSame(
            'Northstar s.r.o.Pobřežní 1Praha186 00Česká republikaIČO: 12345678DIČ: CZ12345678E-mail: office@example.comTelefon aplikace: +420 222 000 000',
            $this->xpath($czech)->evaluate('string(//p)'),
        );

        $english = $emails->create(locale: 'en')->add($address)->render();
        $expectedEnglish = 'Northstar s.r.o.Pobřežní 1Praha186 00Česká republikaCompany ID: 12345678Tax ID: CZ12345678Email: office@example.comPhone: +420 222 000 000';
        self::assertSame($expectedEnglish, $this->xpath($english)->evaluate('string(//p)'));

        $fallback = $emails->create(locale: 'fr')->add($address)->render();
        self::assertSame($expectedEnglish, $this->xpath($fallback)->evaluate('string(//p)'));
    }
}
