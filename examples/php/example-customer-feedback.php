<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;

/** @var EmailGenerator $emails */

$feedbackBaseUrl = 'https://example.com/feedback/CS-7821';

return $emails->create(
    locale: 'en',
    title: 'How did we do with case CS-7821?',
    preheader: 'A one-click rating helps the Northstar support team improve.',
)->add(new Badge('Case resolved', BadgeVariant::Success))
    ->heading('How was your support experience?', alignment: HorizontalAlignment::Center)
    ->text(
        'Hi Riley, Nina marked your data import question as resolved. A quick rating helps us understand what worked and where we can improve.',
        alignment: HorizontalAlignment::Center,
    )
    ->add(new Quote(
        content: 'The import now recognizes our custom date fields and all 1,284 customer records are available in the workspace.',
        attribution: 'Resolution summary · Case CS-7821',
    ))
    ->add(new Group(
        children: [
            new Text('CHOOSE A RATING', HorizontalAlignment::Center),
            new ButtonGroup(
                buttons: [
                    new Button('Great', $feedbackBaseUrl . '?rating=great'),
                    new Button('Okay', $feedbackBaseUrl . '?rating=okay', ButtonVariant::Secondary),
                    new Button('Needs work', $feedbackBaseUrl . '?rating=poor', ButtonVariant::Secondary),
                ],
                gap: 10,
                alignment: HorizontalAlignment::Center,
            ),
        ],
        backgroundColor: new Color('f8fafc'),
        padding: 24,
        borderColor: new Color('e2e8f0'),
    ))
    ->text(new RichText(
        'Want to share more detail? ',
        TextRun::link('Open the two-minute feedback form', $feedbackBaseUrl . '/details'),
        '. Your comments are reviewed by the support team and its managers.',
    ), alignment: HorizontalAlignment::Center)
    ->divider()
    ->add(new Signature(new Contact(
        name: 'Nina Patel',
        position: 'Customer Support Specialist',
        organization: 'Northstar',
        email: 'nina.patel@example.com',
    )))
    ->text('You received this message because you participated in support case CS-7821.')
    ->render();
