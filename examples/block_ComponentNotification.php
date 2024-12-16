<?php

use Lemonade\EmailGenerator\Blocks\Component\ComponentNotification;
use Lemonade\EmailGenerator\DefaultContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

// 1. Initializing ContainerBuilder with context
$container = DefaultContainerBuilder::create();

// 2. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center
$blockManager->setPageTitle(title: "ComponentNotification"); // Set page title

$heading = "What is Lorem Ipsum?";
$notification = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \n \n It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum";

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(new ComponentNotification(contextService: $contextService, heading: $heading, notification: $notification)); // Notification block

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email
