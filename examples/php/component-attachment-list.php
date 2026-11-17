<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Attachment list composition')
    ->add(new AttachmentList([
        new AttachmentItem(
            filename: 'Contract.pdf',
            bytes: 1_800_000,
            url: 'https://example.com/documents/contract.pdf',
        ),
        new AttachmentItem('Price-offer.pdf', bytes: 840_000),
        new AttachmentItem('Technical-specification.xlsx', bytes: 245_000),
    ]))
    ->render();
