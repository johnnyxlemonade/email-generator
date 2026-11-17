<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

return $emails->create(
    locale: 'en',
    title: 'Your Northstar verification code',
    preheader: 'Use code 482 913 to finish signing in. It expires in 10 minutes.',
)->add(new Badge('Secure sign-in', BadgeVariant::Info))
    ->heading('Your verification code')
    ->text('Enter this code in the Northstar sign-in window. Do not share it with anyone, including Northstar support.')
    ->add(new Group(
        children: [
            new Text('ONE-TIME CODE', HorizontalAlignment::Center),
            new Heading('482 913', level: 2, alignment: HorizontalAlignment::Center),
            new Text('Expires at 09:28 UTC', HorizontalAlignment::Center),
        ],
        backgroundColor: new Color('eff6ff'),
        padding: 24,
        borderColor: new Color('93c5fd'),
    ))
    ->add(KeyValue::listing(
        new KeyValue('Sign-in location', 'Dublin, Ireland'),
        new KeyValue('Device', 'Safari on iPhone'),
        new KeyValue('Requested', 'August 3, 2027 at 09:18 UTC'),
    ))
    ->add(new Alert(
        content: new RichText(
            TextRun::strong('Not signing in?'),
            ' Someone may know your password. Change it immediately and review active sessions.',
        ),
        variant: AlertVariant::Error,
    ))
    ->divider()
    ->text('This code is valid for one sign-in attempt and will expire automatically after 10 minutes.')
    ->render();
