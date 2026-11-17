<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;
use Lemonade\Framework\Core\Context\ApplicationContext;
use Lemonade\Framework\Core\Context\DebugMode;
use Lemonade\Framework\Core\Context\Environment;
use Lemonade\Framework\Core\Context\Path;

$examplesDirectory = dirname(__DIR__);
$container = new Container();
$context = new ApplicationContext(
    Environment::Testing,
    new Path($examplesDirectory, $examplesDirectory . '/public'),
    new DebugMode(false),
);
(new CliKernelFactory($container))->create($context)->bootstrap();

return $container->get(EmailGenerator::class);
