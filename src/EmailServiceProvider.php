<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Configuration\EmailConfigDefinition;
use Lemonade\EmailGenerator\Configuration\EmailConfigResolver;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\Framework\Container\ContainerBuilderInterface;
use Lemonade\Framework\Container\ContainerInterface;
use Lemonade\Framework\Core\Config\Definition\ConfigDefinitionRegistry;
use Lemonade\Framework\Core\ServiceProviderInterface;
use Lemonade\Framework\Localization\Config\LocalizationConfig;
use Lemonade\Framework\Localization\TranslationResourceRegistry;
use Lemonade\Framework\Localization\TranslatorInterface;

/**
 * Registers shared email services using framework configuration and localization
 */
final class EmailServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers singleton configuration, rendering and email generation services
     */
    public function register(ContainerBuilderInterface $container): void
    {
        $container->get(TranslationResourceRegistry::class)->register(
            dirname(__DIR__) . '/resources/Language',
            self::class,
        );

        $container->singleton(EmailConfigResolver::class, EmailConfigResolver::class);

        $container->singleton(
            EmailConfig::class,
            static fn(ContainerInterface $container): EmailConfig => $container
                ->get(EmailConfigResolver::class)
                ->resolve(...$container->get(ConfigDefinitionRegistry::class)->typedEntriesFor(
                    EmailConfigDefinition::moduleKey(),
                    EmailConfigDefinition::class,
                )),
        );

        $container->singleton(
            HtmlRenderer::class,
            static fn(ContainerInterface $container): HtmlRenderer => new HtmlRenderer(
                $container->get(TranslatorInterface::class),
                $container->get(EmailConfig::class),
            ),
        );

        $container->singleton(
            EmailGenerator::class,
            static fn(ContainerInterface $container): EmailGenerator => new EmailGenerator(
                $container->get(HtmlRenderer::class),
                $container->get(LocalizationConfig::class)->defaultLocale,
            ),
        );
    }
}
