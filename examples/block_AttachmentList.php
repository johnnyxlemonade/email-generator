<?php


use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
use Lemonade\EmailGenerator\DefaultContainerBuilder;
use Lemonade\EmailGenerator\DTO\AttachmentData;

require __DIR__ . '/../vendor/autoload.php';


// 1. Initializing ContainerBuilder with context
$container = DefaultContainerBuilder::create();

// 2. Attachments
$attachmentService     = $container->getAttachmentCollectionService(); // Attachment service
$attachmentCollection  = $attachmentService->createCollection(); // Attachment collection

// 3. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center
$blockManager->setPageTitle(title: "AttachmentList"); // Set page title

// Adding attachment to the collection
for ($i = 1; $i <= 3; $i++) {

    $attachmentData = new AttachmentData(
        name: 'AttachmentItem ' . $i,
        link: 'https://google.com',
        size: '1MB',
        extension: 'pdf'
    );

    $attachmentService->createItem(collection: $attachmentCollection, data: $attachmentData);
}

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(block: new AttachmentList(contextService: $contextService, collection: $attachmentCollection)); // Attachments list block

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email
