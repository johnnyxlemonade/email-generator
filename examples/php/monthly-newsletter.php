<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$releaseUrl = 'https://example.com/lemonade/releases/may-2027';
$documentationUrl = 'https://example.com/lemonade/docs';
$githubUrl = 'https://github.com/example/lemonade-framework';
$communityUrl = 'https://example.com/lemonade/community';
$softGreen = new Color('f0fdf4');
$softLime = new Color('f7fee7');
$border = new Color('bbf7d0');

return $emails->create(
    locale: 'en',
    title: 'Lemonade Monthly — May 2027',
    preheader: 'A focused release, new email patterns and community notes from Lemonade Framework.',
)->text(new RichText(
    TextRun::strong('LEMONADE FRAMEWORK'),
    "\nMONTHLY · MAY 2027 · ISSUE 08",
))->divider(new Color('15803d'), thickness: 3)
    ->image(
        url: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
        alt: 'A developer workspace with source code on screen',
        width: 536,
        height: 268,
    )
    ->add(new Badge('May release', BadgeVariant::Success))
    ->heading('A smaller surface, a stronger foundation')
    ->text(new RichText(
        'This month is about ',
        TextRun::strong('clarity over volume'),
        '. Lemonade Framework now ships a tighter email API, more explicit component contracts and examples that can be read alongside their generated output.',
    ))
    ->add(new Group(
        children: [
            new Heading('The main story: email composition without template drift', level: 2),
            new Text('Typed, immutable components now cover multi-action groups and accessible process steps while preserving one renderer registry and predictable HTML output.'),
            new Text(new RichText(
                'The release notes explain the design decisions, validation rules and compatibility boundaries behind the new API. ',
                TextRun::strong('No client-specific markup leaks into application code.'),
            )),
        ],
        backgroundColor: $softGreen,
        padding: 24,
        borderColor: $border,
    ))
    ->button('Read the May release notes', $releaseUrl)
    ->spacer(8)
    ->heading('Selected stories', level: 2)
    ->add(new Columns(
        first: [new Group(
            children: [
                new Image(
                    'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=720&q=80',
                    'Developers collaborating around a laptop',
                    width: 360,
                    height: 216,
                ),
                new Badge('Engineering', BadgeVariant::Info),
                new Heading('Immutable by default', level: 3),
                new Text('How document branching, collection snapshots and explicit composition boundaries make transactional email code easier to reason about.'),
                new Text(new RichText(TextRun::link('Explore the architecture guide', $documentationUrl . '/architecture'))),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 12,
            borderColor: new Color('e2e8f0'),
        )],
        second: [new Group(
            children: [
                new Image(
                    'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=720&q=80',
                    'A workshop team reviewing ideas together',
                    width: 360,
                    height: 216,
                ),
                new Badge('Community', BadgeVariant::Neutral),
                new Heading('Patterns you can inspect', level: 3),
                new Text('Every public component now has a minimal PHP source and a matching HTML preview, from RichText to responsive ButtonGroup layouts.'),
                new Text(new RichText(TextRun::link('Browse the example catalog', $documentationUrl . '/examples'))),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 12,
            borderColor: new Color('e2e8f0'),
        )],
        gap: 16,
    ))
    ->add(new Group(
        children: [new Columns(
            first: [
                new Heading('In the framework', level: 3),
                new BulletList([
                    'Clearer renderer contracts and width propagation.',
                    'Stable machine-readable validation codes.',
                    'Responsive action groups with Outlook fallback markup.',
                ]),
            ],
            second: [
                new Heading('Around the community', level: 3),
                new BulletList([
                    'A new CLI application starter is available for review.',
                    'The documentation working group meets on May 26.',
                    new RichText('Share your project in the ', TextRun::link('community showcase', $communityUrl), '.'),
                ]),
            ],
            gap: 24,
        )],
        backgroundColor: $softLime,
        padding: 20,
        borderColor: new Color('d9f99d'),
    ))
    ->heading('More updates in brief', level: 2)
    ->add(new BulletList([
        new RichText(TextRun::strong('Localization:'), ' regional locale handling is documented end to end.'),
        new RichText(TextRun::strong('Quality:'), ' examples are regenerated and compared in the integration suite.'),
        new RichText(TextRun::strong('Compatibility:'), ' MSO fallbacks remain explicit without claiming untested inbox rendering.'),
        new RichText(TextRun::strong('Next month:'), ' configuration ergonomics and delivery integrations enter discovery.'),
    ]))
    ->divider(new Color('d9f99d'), thickness: 2)
    ->add(new Group(
        children: [
            new Heading('Keep building with Lemonade', level: 2),
            new Text(new RichText(
                TextRun::link('Read the documentation', $documentationUrl),
                ' · ',
                TextRun::link('Follow development on GitHub', $githubUrl),
                ' · ',
                TextRun::link('Join the community', $communityUrl),
            )),
            new Text("Lemonade Framework Monthly\nA fictional editorial newsletter for demonstration purposes."),
        ],
        backgroundColor: new Color('f8fafc'),
        padding: 20,
    ))
    ->render();
