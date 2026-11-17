<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$resetUrl = 'https://example.com/password/reset?token=demo-reset-7f31';
$securityUrl = 'https://example.com/account/security/activity';

return $emails->create(
    locale: 'en',
    title: 'Reset your Northstar password',
    preheader: 'Use this secure link within 30 minutes to choose a new password.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR ACCOUNT'),
    "\nPassword assistance",
))->divider(new Color('2563eb'), thickness: 3)
    ->add(new Badge('Action requested', BadgeVariant::Info))
    ->heading('Reset your password')
    ->text('Hi Jordan, we received a request to reset the password for your Northstar account. Use the secure button below to choose a new password.')
    ->button('Choose a new password', $resetUrl)
    ->add(new Alert(
        content: 'For your security, this link can be used once and expires at 14:42 UTC on June 18, 2027.',
        heading: 'Link expires in 30 minutes',
        variant: AlertVariant::Warning,
    ))
    ->add(new Group(
        children: [KeyValue::listing(
            new KeyValue('Account', 'jordan.lee@example.com'),
            new KeyValue('Requested', 'June 18, 2027 at 14:12 UTC'),
            new KeyValue('Request ID', 'PWD-7F31-2027'),
        )],
        backgroundColor: new Color('f8fafc'),
        padding: 16,
        borderColor: new Color('e2e8f0'),
    ))
    ->heading('Did not request this?', level: 2)
    ->add(new Text(new RichText(
        'You can safely ignore this email; your password has not changed. If you notice other unusual activity, ',
        TextRun::link('review your account security', $securityUrl),
        ' and sign out sessions you do not recognize.',
    )))
    ->divider()
    ->text('Northstar Security will never ask you to send your password or verification codes by email.')
    ->render();
