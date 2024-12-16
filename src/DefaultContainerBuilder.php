<?php

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Psr\Log\LogLevel;

class DefaultContainerBuilder
{
    /**
     * Creates and returns a fully configured ContainerBuilder instance.
     *
     * @param SupportedLanguage $language The language to use, defaults to LANG_CS.
     * @return ContainerBuilder
     */
    public static function create(SupportedLanguage $language = SupportedLanguage::LANG_CS): ContainerBuilder
    {
        $logger = new FileLogger(new FileLoggerConfig(logLevel: LogLevel::WARNING));
        $translator = new Translator(currentLanguage: $language, logger: $logger);
        $templateRenderer = new TemplateRenderer(logger: $logger, translator: $translator);
        $blockManager = new BlockManager(templateRenderer: $templateRenderer, logger: $logger, translator: $translator);
        $serviceFactoryManager = new ServiceFactoryManager();

        return new ContainerBuilder(
            logger: $logger,
            translator: $translator,
            templateRenderer: $templateRenderer,
            blockManager: $blockManager,
            serviceFactoryManager: $serviceFactoryManager
        );
    }
}