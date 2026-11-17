<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Configuration\EmailConfigDefinition;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;
use Lemonade\Framework\Core\Config\Definition\ConfigDefinitionRegistry;
use Lemonade\Framework\Core\Context\ApplicationContext;
use Lemonade\Framework\Core\Context\DebugMode;
use Lemonade\Framework\Core\Context\Environment;
use Lemonade\Framework\Core\Context\Path;

/** @var EmailWidth $emailWidth */
$emailWidth = $emailWidth ?? EmailWidth::Standard;
$examplesDirectory = dirname(__DIR__);
$container = new Container();
$context = new ApplicationContext(
    Environment::Testing,
    new Path($examplesDirectory, $examplesDirectory . '/public'),
    new DebugMode(false),
);
(new CliKernelFactory($container))->create($context)->bootstrap();

// Apply the requested variant after application YAML so ordered configuration resolution selects it.
$container->get(ConfigDefinitionRegistry::class)->addDefinition(
    EmailConfigDefinition::create()->width($emailWidth),
);

return $container->get(EmailGenerator::class);
