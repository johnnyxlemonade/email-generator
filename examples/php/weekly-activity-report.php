<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$dashboardUrl = 'https://example.com/workspace/atlas/analytics?period=2027-W23';
$automationUrl = 'https://example.com/workspace/atlas/automations/review';
$blueBackground = new Color('eff6ff');
$blueBorder = new Color('bfdbfe');
$slateBackground = new Color('f8fafc');

return $emails->create(
    locale: 'en',
    title: 'Atlas weekly activity report — June 7–13, 2027',
    preheader: 'Your workspace completed 18,420 automated actions with a 98.7% success rate.',
)->text(new RichText(
    TextRun::strong('ATLAS WORKSPACE'),
    "\nWeekly activity report · June 7–13, 2027",
))->divider(new Color('2563eb'), thickness: 3)
    ->add(new Badge('Healthy workspace', BadgeVariant::Success))
    ->heading('A productive week for your team')
    ->text('Here is the operational summary for Atlas. Activity increased, automation reliability stayed high and one workflow needs attention before next week.')
    ->heading('Key performance indicators', level: 2)
    ->add(new Columns(
        first: [
            new Group(
                children: [
                    new Text('ACTIVE TEAM MEMBERS'),
                    new Heading('148', level: 2),
                    new Text(new RichText(TextRun::strong('+12'), ' compared with last week')),
                ],
                backgroundColor: $blueBackground,
                padding: 16,
                borderColor: $blueBorder,
            ),
            new Group(
                children: [
                    new Text('COMPLETED PROJECT ITEMS'),
                    new Heading('326', level: 2),
                    new Text('41 more than the previous week'),
                ],
                backgroundColor: $slateBackground,
                padding: 16,
                borderColor: new Color('e2e8f0'),
            ),
        ],
        second: [
            new Group(
                children: [
                    new Text('AUTOMATED ACTIONS'),
                    new Heading('18,420', level: 2),
                    new Text('98.7% completed successfully'),
                ],
                backgroundColor: $blueBackground,
                padding: 16,
                borderColor: $blueBorder,
            ),
            new Group(
                children: [
                    new Text('MEDIAN RESPONSE TIME'),
                    new Heading('2h 14m', level: 2),
                    new Text('18 minutes faster than last week'),
                ],
                backgroundColor: $slateBackground,
                padding: 16,
                borderColor: new Color('e2e8f0'),
            ),
        ],
        gap: 16,
    ))
    ->heading('Activity overview', level: 2)
    ->table(
        headers: ['Metric', 'This week', 'Change'],
        rows: [
            ['New project items', '412', '+6.7%'],
            ['Completed project items', '326', '+14.4%'],
            ['Comments and decisions', '1,284', '+7.3%'],
            ['Files shared', '238', '−5.2%'],
            ['Automation failures', '239', '+29.9%'],
        ],
    )
    ->heading('What changed this week', level: 2)
    ->add(new Columns(
        first: [
            new Badge('Momentum', BadgeVariant::Info),
            new Heading('Delivery accelerated', level: 3),
            new Text('The product and research teams closed 14% more work while keeping median response time below three hours.'),
        ],
        second: [
            new Badge('Watch', BadgeVariant::Warning),
            new Heading('One automation is noisy', level: 3),
            new Text('The “Customer handoff” workflow produced 71% of all failures after a connected field was renamed.'),
        ],
        gap: 24,
    ))
    ->add(new Group(
        children: [
            new Heading('Recommended actions', level: 2),
            new BulletList([
                new RichText(TextRun::strong('Repair the Customer handoff workflow'), ' before Monday’s first scheduled run.'),
                'Review the 24 project items that have been inactive for more than 14 days.',
                'Recognize the research team for reducing response time by 22%.',
            ]),
        ],
        backgroundColor: new Color('fff7ed'),
        padding: 20,
        borderColor: new Color('fdba74'),
    ))
    ->button('Open the full dashboard', $dashboardUrl)
    ->text(new RichText(
        'Need to fix the failing workflow first? ',
        TextRun::link('Review automation errors', $automationUrl),
        '.',
    ))
    ->divider(new Color('cbd5e1'))
    ->text("Atlas Workspace · Weekly report for workspace administrators\nThis is a fictional activity report generated for demonstration purposes.")
    ->render();
