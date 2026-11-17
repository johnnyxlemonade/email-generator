<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$approveUrl = 'https://example.com/approvals/APR-2027-0864/approve';
$rejectUrl = 'https://example.com/approvals/APR-2027-0864/reject';
$detailUrl = 'https://example.com/approvals/APR-2027-0864';

return $emails->create(
    locale: 'en',
    title: 'Approval needed — regional support coverage',
    preheader: 'Maya Chen is waiting for your decision on request APR-2027-0864 by November 18.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR OPERATIONS'),
    "\nDecision centre · Request APR-2027-0864",
))->divider(new Color('7c3aed'), thickness: 3)
    ->add(new Badge('Decision required', BadgeVariant::Warning))
    ->heading('Approve expanded support coverage')
    ->text('Maya Chen submitted a change request for the Customer Operations team. Your decision is required before the December staffing schedule can be published.')
    ->add(new Group(
        children: [
            new Text('REQUEST'),
            new Heading('Regional support coverage — Q1 2028', level: 2),
            new Text(new RichText(
                'Requested by ',
                TextRun::strong('Maya Chen'),
                "\nDirector of Customer Operations",
            )),
        ],
        backgroundColor: new Color('f5f3ff'),
        padding: 24,
        borderColor: new Color('c4b5fd'),
    ))
    ->add(new Columns(
        first: [new Group(
            children: [
                new Text('SUBMITTED'),
                new Heading('November 12', level: 3),
                new Text('2027 · 09:42 GMT'),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
            borderColor: new Color('e2e8f0'),
        )],
        second: [new Group(
            children: [
                new Text('DECISION DUE'),
                new Heading('November 18', level: 3),
                new Text(new RichText(TextRun::strong('4 business days remaining'))),
            ],
            backgroundColor: new Color('fff7ed'),
            padding: 16,
            borderColor: new Color('fdba74'),
        )],
        gap: 16,
    ))
    ->add(KeyValue::listing(
        new KeyValue('Request ID', 'APR-2027-0864'),
        new KeyValue('Current status', new RichText(TextRun::strong('Waiting for department approval'))),
        new KeyValue('Priority', 'High — schedule dependency'),
        new KeyValue('Cost centre', 'Customer Operations · 4102'),
    ))
    ->heading('Requested changes', level: 2)
    ->table(
        headers: ['Change', 'Current', 'Requested'],
        rows: [
            ['Weekday coverage', '08:00–18:00 GMT', '07:00–21:00 GMT'],
            ['Weekend coverage', 'Emergency only', '08:00–16:00 GMT'],
            ['On-call specialists', '2 per week', '4 per week'],
            ['Quarterly budget', '$84,000', '$112,000'],
        ],
    )
    ->add(new Alert(
        content: 'Finance has confirmed that the additional $28,000 fits within the approved Q1 operating plan. Your decision releases the request to Workforce Planning.',
        heading: 'Budget check completed',
        variant: AlertVariant::Success,
    ))
    ->add(new Quote(
        content: 'Extending coverage will remove the six-hour handoff gap for West Coast customers and gives the team enough capacity for the January launch.',
        attribution: 'Maya Chen · requester note',
    ))
    ->add(new ButtonGroup([
        new Button('Approve request', $approveUrl),
        new Button('Reject request', $rejectUrl, ButtonVariant::Secondary),
        new Button('Open details', $detailUrl, ButtonVariant::Secondary),
    ]))
    ->text('Approval and rejection links are assigned to you and should not be forwarded. A reason can be added before the final decision is submitted.')
    ->render();
