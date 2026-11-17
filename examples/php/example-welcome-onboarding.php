<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Hero;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\ImageSource;

/** @var EmailGenerator $emails */

$workspaceUrl = 'https://example.com/workspaces/northstar-studio';
$profileUrl = 'https://example.com/account/profile';
$guideUrl = 'https://example.com/guides/getting-started';

return $emails->create(
    locale: 'en',
    title: 'Welcome to Northstar Workspace',
    preheader: 'Your workspace is ready. Complete three quick steps to get your team moving.',
)->add(new Hero(
    heading: 'Welcome to Northstar, Alex',
    description: new RichText(
        'Your new workspace is ready for projects, decisions and team knowledge. ',
        TextRun::strong('Start with the essentials below'),
        ' and invite the rest of your team when you are comfortable.',
    ),
    image: new ImageSource(
        'https://placehold.co/1200x480/0f766e/ffffff/png?text=Welcome+to+Northstar',
        'Northstar workspace welcome banner',
    ),
    button: new Button('Open your workspace', $workspaceUrl),
))->add(new Steps(
    steps: ['Create your profile', 'Set up the first project', 'Invite your team'],
    currentStep: 1,
))->add(new Columns(
    first: [new Group(
        children: [
            new Heading('Make it yours', level: 3),
            new Text('Add a photo, preferred name and notification settings so teammates know how to reach you.'),
            new Button('Complete profile', $profileUrl, ButtonVariant::Secondary),
        ],
        backgroundColor: new Color('f0fdfa'),
        padding: 20,
        borderColor: new Color('99f6e4'),
    )],
    second: [new Group(
        children: [
            new Heading('Build with a template', level: 3),
            new Text('Use a ready-made project structure for product launches, client delivery or internal operations.'),
            new Button('Browse the guide', $guideUrl, ButtonVariant::Secondary),
        ],
        backgroundColor: new Color('eff6ff'),
        padding: 20,
        borderColor: new Color('bfdbfe'),
    )],
    gap: 20,
))->add(new Alert(
    content: 'You have a 14-day Team trial. We will remind you before it ends, and no payment method is required to explore the workspace.',
    heading: 'Your trial has started',
    variant: AlertVariant::Info,
))->add(new ButtonGroup(
    buttons: [
        new Button('Open Northstar', $workspaceUrl),
        new Button('Read the getting-started guide', $guideUrl, ButtonVariant::Secondary),
    ],
    alignment: HorizontalAlignment::Center,
))->divider()
    ->text(new RichText(
        'Need a hand? Reply to this email or visit the ',
        TextRun::link('Northstar help center', 'https://example.com/help'),
        '.',
    ), alignment: HorizontalAlignment::Center)
    ->render();
