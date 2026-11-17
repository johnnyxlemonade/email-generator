<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Enum\StepsProgress;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$returnUrl = 'https://example.com/returns/RET-2027-1184';

return $emails->create(
    locale: 'en',
    title: 'Your refund has been issued',
    preheader: 'A refund of $74.50 was sent to your Mastercard ending in 0091.',
)->add(new Badge('Refund issued', BadgeVariant::Success))
    ->heading('Your refund is on its way')
    ->text('Hi Priya, we completed the refund for return RET-2027-1184. No further action is needed from you.')
    ->add(new Group(
        children: [
            new KeyValue('Refund amount', new RichText(TextRun::strong('$74.50'))),
            new KeyValue('Destination', 'Mastercard ending in 0091'),
            new KeyValue('Refund reference', 'RF-8841027'),
        ],
        backgroundColor: new Color('f0fdf4'),
        padding: 20,
        borderColor: new Color('86efac'),
    ))
    ->add(new Steps(
        steps: ['Return received', 'Item inspected', 'Refund issued'],
        currentStep: null,
        progress: StepsProgress::Completed,
    ))
    ->heading('Refund breakdown', level: 2)
    ->table(
        headers: ['Description', 'Amount'],
        rows: [
            ['Everyday overshirt · Navy', '$68.00'],
            ['Sales tax returned', '$6.50'],
            [new RichText(TextRun::strong('Total refund')), new RichText(TextRun::strong('$74.50'))],
        ],
        columnAlignments: [1 => HorizontalAlignment::Right],
    )
    ->add(new Alert(
        content: 'Most banks post refunds within 3–5 business days. The final timing depends on your card issuer and may not match the original transaction date.',
        heading: 'When will it appear?',
        variant: AlertVariant::Info,
    ))
    ->button('View return details', $returnUrl)
    ->divider()
    ->text('Questions? Reply to this email with refund reference RF-8841027 so our returns team can help quickly.')
    ->render();
