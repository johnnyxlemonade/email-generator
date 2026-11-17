<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonGroupAlignment;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$secureAccountUrl = 'https://example.com/account/security/secure?event=login-demo-4821';
$activityUrl = 'https://example.com/account/security/activity?event=login-demo-4821';
$helpUrl = 'https://example.com/help/account-security';

return $emails->create(
    locale: 'en',
    title: 'New sign-in to your Lemonade account',
    preheader: 'Review a new sign-in from Chrome on macOS near Berlin, Germany.',
)->add(new Group(
    children: [new Text(new RichText(
        TextRun::strong('LEMONADE ACCOUNT'),
        "\nSecurity notification",
    ))],
    backgroundColor: new Color('f8fafc'),
    padding: 16,
    borderColor: new Color('e2e8f0'),
))->heading('New sign-in detected')
    ->add(new Badge('Review recommended', BadgeVariant::Warning))
    ->text(new RichText(
        'Hi Alex,',
        "\n\n",
        'your Lemonade account was accessed from a device we have not seen before. Review the details below and confirm whether this activity was yours.',
    ))
    ->heading('Sign-in details', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Device', 'MacBook Pro · macOS 15.4'),
        new KeyValue('Browser', 'Chrome 136'),
        new KeyValue('Date', 'June 12, 2027'),
        new KeyValue('Time', '21:47 CEST'),
        new KeyValue('Approximate location', 'Berlin, Germany'),
        new KeyValue('IP address', '203.0.113.42'),
    ))
    ->add(new Group(
        children: [
            new Heading('Was this you?', level: 2),
            new Text(new RichText(
                TextRun::strong('If you recognize this sign-in, no action is required.'),
                "\nIf you do not recognize it, secure your account immediately. This will help you change your password and review active sessions.",
            )),
        ],
        backgroundColor: new Color('fff7ed'),
        padding: 20,
        borderColor: new Color('fdba74'),
    ))
    ->add(new ButtonGroup(
        buttons: [
            new Button('Secure my account', $secureAccountUrl),
            new Button('Review activity', $activityUrl, ButtonVariant::Secondary),
        ],
        gap: 12,
        alignment: ButtonGroupAlignment::Left,
    ))
    ->heading('Recommended security checks', level: 2)
    ->add(new BulletList([
        'Use a unique password that you do not use for any other service.',
        'Review active sessions and sign out devices you no longer recognize.',
        'Enable multi-factor authentication for an additional verification step.',
        'Check that your recovery email and phone number are still current.',
    ]))
    ->add(new Group(
        children: [new Text(new RichText(
            TextRun::strong('Location is approximate.'),
            ' It is inferred from the network address and may reflect a nearby city, mobile provider or corporate network rather than the device’s exact position.',
        ))],
        backgroundColor: new Color('eff6ff'),
        padding: 16,
        borderColor: new Color('bfdbfe'),
    ))
    ->divider(new Color('cbd5e1'))
    ->text(new RichText(
        'Need help? ',
        TextRun::link('Visit the account security guide', $helpUrl),
        "\nLemonade Account Security · This is a fictional demonstration notification.",
    ))
    ->render();
