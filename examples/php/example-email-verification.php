<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$verificationUrl = 'https://example.com/email/verify?token=demo-verify-91ac';
$accountUrl = 'https://example.com/account';

return $emails->create(
    locale: 'en',
    title: 'Verify your email address',
    preheader: 'Confirm alex.morgan@example.com to finish setting up your account.',
)->spacer(12)
    ->heading('Confirm your email address', alignment: HorizontalAlignment::Center)
    ->text(
        'One quick step keeps your account secure and makes sure important workspace updates reach the right inbox.',
        alignment: HorizontalAlignment::Center,
    )
    ->add(new Group(
        children: [
            new Text('EMAIL TO VERIFY', HorizontalAlignment::Center),
            new Text(
                new RichText(TextRun::strong('alex.morgan@example.com')),
                HorizontalAlignment::Center,
            ),
            new Button('Verify email address', $verificationUrl),
        ],
        backgroundColor: new Color('f0fdf4'),
        padding: 24,
        borderColor: new Color('86efac'),
    ))
    ->add(new Alert(
        content: 'The verification link expires in 24 hours. You can request another link from your account settings if needed.',
        variant: AlertVariant::Info,
    ))
    ->text(new RichText(
        'If the button does not open, visit ',
        TextRun::link('your account settings', $accountUrl),
        ' and request a fresh verification email.',
    ), alignment: HorizontalAlignment::Center)
    ->divider()
    ->text(
        'If you did not create this account, no action is required.',
        alignment: HorizontalAlignment::Center,
    )
    ->render();
