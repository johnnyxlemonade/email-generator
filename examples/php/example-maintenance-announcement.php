<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Hero;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\ImageSource;

/** @var EmailGenerator $emails */

$statusUrl = 'https://status.example.com/incidents/maintenance-2027-11';

return $emails->create(
    locale: 'en',
    title: 'Scheduled maintenance on November 20',
    preheader: 'Northstar will be read-only for up to 45 minutes during a database upgrade.',
)->add(new Hero(
    heading: 'Planned maintenance: November 20',
    description: 'We are upgrading the primary database to improve search performance and reliability across larger workspaces.',
    image: new ImageSource(
        'https://placehold.co/1200x400/172554/ffffff/png?text=Scheduled+Maintenance',
        'Scheduled maintenance announcement',
    ),
    button: new Button('Follow status updates', $statusUrl),
))->add(new Alert(
    content: 'Northstar will be read-only from 02:00 to 02:45 UTC. You can view existing content, but edits, uploads and invitations will be temporarily unavailable.',
    heading: 'Expected impact: up to 45 minutes',
    variant: AlertVariant::Warning,
))->add(new Steps(
    steps: ['Read-only mode begins', 'Database upgrade', 'Verification and full access'],
    currentStep: 1,
))->add(new Columns(
    first: [new Group(
        children: [
            new Heading('Available', level: 3),
            new BulletList([
                'View pages, projects and comments.',
                'Search recently indexed content.',
                'Download existing attachments.',
            ]),
        ],
        backgroundColor: new Color('f0fdf4'),
        padding: 20,
        borderColor: new Color('86efac'),
    )],
    second: [new Group(
        children: [
            new Heading('Temporarily paused', level: 3),
            new BulletList([
                'Editing and publishing content.',
                'Uploading files and importing data.',
                'Inviting or removing members.',
            ]),
        ],
        backgroundColor: new Color('fff7ed'),
        padding: 20,
        borderColor: new Color('fdba74'),
    )],
    gap: 20,
))->heading('Maintenance windows by region', level: 2)
    ->table(
        headers: ['Region', 'Local time'],
        rows: [
            ['UTC', '02:00–02:45'],
            ['Central Europe', '03:00–03:45 CET'],
            ['US Eastern', '21:00–21:45 EST · November 19'],
            ['US Pacific', '18:00–18:45 PST · November 19'],
        ],
    )
    ->text(new RichText(
        'We will post progress and completion updates on the ',
        TextRun::link('Northstar status page', $statusUrl),
        '. No action is required before the maintenance window.',
    ))
    ->render();
