<?php

use Lemonade\EmailGenerator\Blocks\Component\ComponentFormItemList;
use Lemonade\EmailGenerator\DefaultContainerBuilder;
use Lemonade\EmailGenerator\DTO\FormItemData;


require __DIR__ . '/../vendor/autoload.php';

// 1. Initializing ContainerBuilder with context
$container = DefaultContainerBuilder::create();

// 2. Attachments
$attachmentService    = $container->getAttachmentCollectionService(); // Attachment service
$attachmentCollection = $attachmentService->createCollection(); // Attachment collection

// 3. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center
$blockManager->setPageTitle(title: "ComponentFormItemList"); // Set page title

// Form data collection
$formService = $container->getFormItemCollectionService(); // Form item collection service
$formCollection = $formService->createCollection(); // Create a new form item collection

$formData = [ // Sample form data
    ["name" => "Name", "text" => "Franta"],
    ["name" => "Surname", "text" => "Novák"],
    ["name" => "Email", "text" => "franta.novak.12345679@gmail.com"],
    ["name" => "Phone", "text" => "1234567890"],
    ["name" => "Newsletter", "text" => "Yes"],
    ["name" => "Confirmed", "text" => null]
];

foreach($formData as $key => $val) { // Loop through form data and create form items
    $formItem = new FormItemData(name: $val["name"], value: $val["text"]); // Create form item
    $formService->createItem($formCollection, $formItem); // Add form item to collection
}

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(block: new ComponentFormItemList(contextService: $contextService, name: "Contact form", collection: $formCollection)); // Displaying form items in a list

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email
