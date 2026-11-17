<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Configuration\EmailConfigDefinition;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Translation;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;
use Lemonade\Framework\Core\Config\ConfigFileLoader;
use Lemonade\Framework\Core\Config\Definition\ConfigDefinitionRegistry;
use Lemonade\Framework\Localization\TranslatorInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Verifies email services and configuration through the real framework CLI bootstrap
 */
final class ServiceProviderTest extends EmailTestCase
{
    public function testRealCliBootstrapLoadsProviderConfigAndTranslatorWithoutRequest(): void
    {
        $container = new Container();
        $kernel = (new CliKernelFactory($container))->create(self::context());
        $kernel->bootstrap();
        $kernel->bootstrap();
        self::assertFalse($container->isBound(ServerRequestInterface::class));
        self::assertTrue($container->isBound(EmailGenerator::class));
        self::assertSame($container->get(EmailGenerator::class), $container->get(EmailGenerator::class));
        self::assertSame($container->get(HtmlRenderer::class), $container->get(HtmlRenderer::class));
        self::assertSame(EmailWidth::Wide, $container->get(EmailConfig::class)->width());
        self::assertSame('#15803d', $container->get(EmailConfig::class)->accentColor()->hex());
        self::assertCount(1, $container->get(ConfigDefinitionRegistry::class)->entriesFor('email_generator'));
        $container->get(TranslatorInterface::class)->setLocale('en');
        $emails = $container->get(EmailGenerator::class);
        $document = $emails->create()->heading(new Translation('emails.welcome', ['name' => 'CLI']))
            ->button('Go', 'https://example.com');
        self::assertSame('cs', $document->locale());
        self::assertStringContainsString('Vítejte, CLI!', $document->render());
        self::assertSame('en', $container->get(TranslatorInterface::class)->locale());
        self::assertNotSame($emails->create(), $emails->create());
    }

    public function testFrameworkYamlMappingHydratesPackageDefinition(): void
    {
        $definition = (new ConfigFileLoader())->load(__DIR__ . '/../Fixtures/app/Config/Email.yaml');
        self::assertInstanceOf(EmailConfigDefinition::class, $definition);
        self::assertSame(['width' => 800, 'accent_color' => '#15803d'], $definition->toArray());
    }
}
