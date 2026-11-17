<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;

/** @var EmailGenerator $emails */

return $emails->create(
    locale: 'en',
    title: 'Your Northstar project document package',
    preheader: 'The contract, price offer and technical specification are attached to this email.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR ENGINEERING'),
    "\nProject documentation",
))->divider(new Color('0f766e'), thickness: 3)
    ->heading('Your document package is ready')
    ->text(new RichText(
        'Hello Ms. Bennett,',
        "\n\n",
        'thank you for reviewing our proposal for the Riverside monitoring upgrade. The agreed commercial and technical documents are physically attached to this email for your records.',
    ))
    ->add(KeyValue::listing(
        new KeyValue('Project', 'Riverside monitoring upgrade'),
        new KeyValue('Package reference', 'NS-2027-184'),
        new KeyValue('Prepared for', 'Bennett Infrastructure Ltd.'),
        new KeyValue('Valid until', 'December 15, 2027'),
    ))
    ->add(new AttachmentList([
        new AttachmentItem('Contract.pdf', bytes: 1_800_000),
        new AttachmentItem('Price-offer.pdf', bytes: 840_000),
        new AttachmentItem('Technical-specification.xlsx', bytes: 245_000),
    ]))
    ->text('Please reply with the signed contract or send any requested amendments by December 8. I will coordinate the final version with our commercial and engineering teams.')
    ->divider()
    ->add(new Signature(new Contact(
        name: 'Elena Martin',
        position: 'Commercial Project Manager',
        organization: 'Northstar Engineering',
        email: 'elena.martin@example.com',
        phone: '+44 20 7946 0241',
    )))
    ->text('Northstar Engineering Ltd. · 28 Harbor Street, London E14 8PX · Package NS-2027-184')
    ->render();
