<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$activityUrl = 'https://example.com/workspaces/atlas/activity?date=2027-11-16';

/**
 * @var list<array{
 *     title: string,
 *     count: int,
 *     variant: BadgeVariant,
 *     background: string,
 *     border: string,
 *     items: list<array{title: string, detail: string, meta: string}>
 * }> $activityGroups
 */
$activityGroups = [
    [
        'title' => 'Newly assigned tasks',
        'count' => 3,
        'variant' => BadgeVariant::Info,
        'background' => 'eff6ff',
        'border' => '93c5fd',
        'items' => [
            [
                'title' => 'Prepare wayfinding review package',
                'detail' => 'Harbor Renewal · assigned by Jordan Lee',
                'meta' => 'High priority · due Friday at 16:00',
            ],
            [
                'title' => 'Confirm Q1 onboarding capacity',
                'detail' => 'Customer Operations · assigned by Maya Chen',
                'meta' => 'Normal priority · due November 22',
            ],
            [
                'title' => 'Review accessibility test notes',
                'detail' => 'Mobile Portal · assigned by Ethan Cole',
                'meta' => 'Normal priority · due November 24',
            ],
        ],
    ],
    [
        'title' => 'Comments and mentions',
        'count' => 4,
        'variant' => BadgeVariant::Neutral,
        'background' => 'f8fafc',
        'border' => 'cbd5e1',
        'items' => [
            [
                'title' => 'Priya mentioned you in “Utilities markup”',
                'detail' => '“Can you confirm whether the riser location matches the survey?”',
                'meta' => 'Harbor Renewal · 14:32',
            ],
            [
                'title' => 'Two replies on the launch checklist',
                'detail' => 'Operations confirmed the training room and attendee list.',
                'meta' => 'Regional support launch · 12:18',
            ],
            [
                'title' => 'Finance requested a budget note',
                'detail' => 'Add the annualized impact before the approval meeting.',
                'meta' => 'Q1 planning · 10:46',
            ],
        ],
    ],
    [
        'title' => 'Waiting for approval',
        'count' => 2,
        'variant' => BadgeVariant::Warning,
        'background' => 'fffbeb',
        'border' => 'fcd34d',
        'items' => [
            [
                'title' => 'Regional support coverage — Q1 2028',
                'detail' => 'Department approval requested by Maya Chen',
                'meta' => 'High priority · decision due November 18',
            ],
            [
                'title' => 'Vendor security assessment',
                'detail' => 'Final risk acceptance requested by Compliance',
                'meta' => 'Normal priority · decision due November 21',
            ],
        ],
    ],
    [
        'title' => 'Approaching deadlines',
        'count' => 3,
        'variant' => BadgeVariant::Danger,
        'background' => 'fef2f2',
        'border' => 'fca5a5',
        'items' => [
            [
                'title' => 'Wayfinding review package',
                'detail' => '1 day remaining · owner Alex Morgan',
                'meta' => 'Due November 19 at 16:00',
            ],
            [
                'title' => 'November supplier forecast',
                'detail' => '3 days remaining · owner Priya Shah',
                'meta' => 'Due November 21 at 12:00',
            ],
            [
                'title' => 'Accessibility test summary',
                'detail' => '5 days remaining · owner Ethan Cole',
                'meta' => 'Due November 23 at 17:00',
            ],
        ],
    ],
];

$document = $emails->create(
    locale: 'en',
    title: 'Your daily activity digest — November 16',
    preheader: '12 new events: 3 tasks, 4 comments, 2 approvals and 3 approaching deadlines.',
)->text(new RichText(
    TextRun::strong('ATLAS WORKSPACE'),
    "\nDaily digest · Tuesday, November 16, 2027",
))->divider(new Color('4338ca'), thickness: 3)
    ->heading('12 new events since yesterday')
    ->text('Good morning, Alex. Here is the work that needs your attention across the Atlas workspace, grouped by activity type.')
    ->add(new Columns(
        first: [new Group(
            children: [
                new Text('ACTION NEEDED'),
                new Heading('5 items', level: 2),
                new Text('3 assigned tasks and 2 approval decisions'),
            ],
            backgroundColor: new Color('eef2ff'),
            padding: 18,
            borderColor: new Color('a5b4fc'),
        )],
        second: [new Group(
            children: [
                new Text('TIME SENSITIVE'),
                new Heading('3 deadlines', level: 2),
                new Text('The nearest deadline is tomorrow at 16:00'),
            ],
            backgroundColor: new Color('fff7ed'),
            padding: 18,
            borderColor: new Color('fdba74'),
        )],
        gap: 16,
    ));

foreach ($activityGroups as $activityGroup) {
    /** @var list<ComponentInterface> $section */
    $section = [
        new Badge($activityGroup['count'] . ' new', $activityGroup['variant']),
        new Heading($activityGroup['title'], level: 2),
    ];

    foreach ($activityGroup['items'] as $item) {
        $section[] = new Text(new RichText(
            TextRun::strong($item['title']),
            "\n" . $item['detail'],
            "\n" . $item['meta'],
        ));
    }

    $document = $document->add(new Group(
        children: $section,
        backgroundColor: new Color($activityGroup['background']),
        padding: 20,
        borderColor: new Color($activityGroup['border']),
    ));
}

return $document
    ->button('Open activity overview', $activityUrl)
    ->text(new RichText(
        'This digest includes events recorded between November 15 at 17:00 and November 16 at 17:00 GMT. ',
        TextRun::link('Manage notification preferences', 'https://example.com/settings/notifications'),
        '.',
    ))
    ->divider(new Color('cbd5e1'))
    ->text("Atlas Workspace · Daily administrative digest\nYou receive this summary on business days because daily notifications are enabled.")
    ->render();
