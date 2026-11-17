<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;

/** @var EmailGenerator $emails */

$taskUrl = 'https://example.com/projects/harbor-renewal/tasks/TASK-1842';
$briefUrl = 'https://example.com/projects/harbor-renewal/files/wayfinding-brief';

return $emails->create(
    locale: 'en',
    title: 'New task assigned — prepare wayfinding review',
    preheader: 'Jordan Lee assigned you a high-priority task due Friday, November 19.',
)->text(new RichText(
    TextRun::strong('ATLAS PROJECTS'),
    "\nHarbor Renewal · New assignment",
))->divider(new Color('ea580c'), thickness: 3)
    ->add(new Badge('High priority', BadgeVariant::Danger))
    ->heading('Prepare wayfinding package for review')
    ->text('Jordan Lee assigned this task to you in the Harbor Renewal project. The review package will be used for Monday’s accessibility and operations workshop.')
    ->add(new Group(
        children: [
            new Text('TASK BRIEF'),
            new Heading('Consolidate the revised sign schedule, floor plans and open design decisions into one review package.', level: 2),
            new Text('Include the latest room numbering, flag any locations that still need a site measurement and assign each unresolved comment to an owner.'),
        ],
        backgroundColor: new Color('fff7ed'),
        padding: 24,
        borderColor: new Color('fb923c'),
    ))
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Assigned by', level: 3),
                new Text(new RichText(
                    TextRun::strong('Jordan Lee'),
                    "\nDesign Lead",
                    "\nNovember 15 at 14:18 GMT",
                )),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 18,
            borderColor: new Color('e2e8f0'),
        )],
        second: [new Group(
            children: [
                new Heading('Assigned to', level: 3),
                new Text(new RichText(
                    TextRun::strong('Alex Morgan'),
                    "\nProject Coordinator",
                    "\nDue November 19 at 16:00 GMT",
                )),
            ],
            backgroundColor: new Color('fef2f2'),
            padding: 18,
            borderColor: new Color('fca5a5'),
        )],
        gap: 16,
    ))
    ->add(KeyValue::listing(
        new KeyValue('Task ID', 'TASK-1842'),
        new KeyValue('Project', 'Harbor Renewal · Interior fit-out'),
        new KeyValue('Status', new RichText(TextRun::strong('Not started'))),
        new KeyValue('Priority', 'High'),
        new KeyValue('Estimated effort', '6 hours'),
    ))
    ->heading('Related documents', level: 2)
    ->add(new AttachmentList([
        new AttachmentItem('Wayfinding-design-brief.pdf', 1_180_000, $briefUrl),
        new AttachmentItem('Sign-schedule-revision-C.xlsx', 486_000, $taskUrl . '/files/sign-schedule'),
        new AttachmentItem('Level-01-and-02-markups.pdf', 5_720_000, $taskUrl . '/files/floor-plan-markups'),
    ]))
    ->add(new Quote(
        content: 'Please keep the package concise. I mainly need a reliable view of what is approved, what is blocked and who needs to make each remaining decision.',
        attribution: 'Jordan Lee · assignment comment',
    ))
    ->button('Open task and start work', $taskUrl)
    ->text(new RichText(
        'Questions about scope? Add a comment on the task or contact Jordan before Thursday at 12:00 GMT so there is time to adjust the workshop agenda.',
    ))
    ->divider()
    ->add(new Signature(new Contact(
        name: 'Jordan Lee',
        position: 'Design Lead',
        organization: 'Harbor Renewal Project',
        email: 'jordan.lee@example.com',
        phone: '+44 20 7946 0318',
    )))
    ->render();
