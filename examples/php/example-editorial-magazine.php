<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Hero;
use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\ImageSource;

/** @var EmailGenerator $emails */

$featureUrl = 'https://example.com/field-notes/issues/28/quiet-infrastructure';

return $emails->create(
    locale: 'en',
    title: 'Field Notes No. 28 — The quiet infrastructure issue',
    preheader: 'A monthly journal about the systems, spaces and decisions behind meaningful work.',
)->text('FIELD NOTES · ISSUE 28', alignment: HorizontalAlignment::Center)
    ->divider(new Color('1e293b'), thickness: 3)
    ->add(new Hero(
        heading: 'The quiet infrastructure behind good work',
        description: 'This month, we look past the visible launch and into the rituals, archives and physical spaces that allow thoughtful teams to keep moving.',
        image: new ImageSource(
            'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1400&q=80',
            'A calm collaborative studio with plants and natural light',
        ),
        button: new Button('Read the cover story', $featureUrl),
    ))
    ->add(new Quote(
        content: 'Infrastructure is not the background of the work. It is the set of choices that decides which work remains possible.',
        attribution: 'Mara Bell · Editor’s letter',
    ))
    ->heading('Two perspectives', level: 2)
    ->add(new Columns(
        first: [new Group(
            children: [
                new Image(
                    'https://images.unsplash.com/photo-1483058712412-4245e9b90334?auto=format&fit=crop&w=800&q=80',
                    'A desk prepared for focused work',
                    width: 520,
                    height: 320,
                ),
                new Heading('Designing a useful archive', level: 3),
                new Text('What a ten-year research library taught one studio about naming, retrieval and deciding what deserves to last.'),
                new Text(new RichText(TextRun::link('Read the essay →', 'https://example.com/field-notes/archive'))),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
        )],
        second: [new Group(
            children: [
                new Image(
                    'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=800&q=80',
                    'A team meeting in a bright project room',
                    width: 520,
                    height: 320,
                ),
                new Heading('The meeting after the meeting', level: 3),
                new Text('A field guide to documenting the informal decisions that shape projects long after the calendar event ends.'),
                new Text(new RichText(TextRun::link('Open the field guide →', 'https://example.com/field-notes/decisions'))),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
        )],
        gap: 20,
    ))
    ->heading('Notes in brief', level: 2)
    ->add(new BulletList([
        new RichText(TextRun::strong('Observed:'), ' why teams are returning to visible, physical project boards.'),
        new RichText(TextRun::strong('Collected:'), ' five templates for preserving context without creating paperwork.'),
        new RichText(TextRun::strong('Recommended:'), ' a short book on maintenance as a creative practice.'),
    ]))
    ->divider(new Color('1e293b'), thickness: 2)
    ->text(
        'Field Notes is an independent monthly journal published by Northstar Press.',
        alignment: HorizontalAlignment::Center,
    )
    ->render();
