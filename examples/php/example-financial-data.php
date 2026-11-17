<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Columns;
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

$reportUrl = 'https://example.com/finance/reports/2027-09';

return $emails->create(
    locale: 'en',
    title: 'September financial summary — Northstar Operations',
    preheader: 'Revenue closed at $428,600 with a 21.4% operating margin.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR OPERATIONS'),
    "\nSeptember 2027 management report",
))->divider(new Color('0f172a'), thickness: 3)
    ->add(new Badge('Month closed', BadgeVariant::Success))
    ->heading('September financial summary')
    ->text('Revenue finished ahead of plan while operating expenses remained within the approved quarterly range. The figures below are preliminary until the board package is signed off.')
    ->add(new Columns(
        first: [new Group(
            children: [
                new Text('NET REVENUE'),
                new Heading('$428,600', level: 2),
                new Text(new RichText(TextRun::strong('+8.2%'), ' vs. August')),
            ],
            backgroundColor: new Color('f0fdf4'),
            padding: 20,
            borderColor: new Color('86efac'),
        )],
        second: [new Group(
            children: [
                new Text('OPERATING MARGIN'),
                new Heading('21.4%', level: 2),
                new Text(new RichText(TextRun::strong('+1.6 pts'), ' vs. plan')),
            ],
            backgroundColor: new Color('eff6ff'),
            padding: 20,
            borderColor: new Color('93c5fd'),
        )],
        gap: 16,
    ))
    ->heading('Performance by business line', level: 2)
    ->table(
        headers: ['Business line', 'Actual', 'Plan', 'Variance'],
        rows: [
            ['Subscriptions', '$264,200', '$250,000', '+5.7%'],
            ['Professional services', '$121,900', '$118,000', '+3.3%'],
            ['Training and events', '$42,500', '$48,000', '−11.5%'],
            [new RichText(TextRun::strong('Net revenue')), new RichText(TextRun::strong('$428,600')), '$416,000', '+3.0%'],
        ],
        columnAlignments: [
            1 => HorizontalAlignment::Right,
            2 => HorizontalAlignment::Right,
            3 => HorizontalAlignment::Right,
        ],
    )
    ->add(KeyValue::listing(
        new KeyValue('Operating expenses', '$336,900'),
        new KeyValue('Cash balance', '$1,284,000'),
        new KeyValue('Accounts receivable', '$96,400'),
        new KeyValue('Runway at current plan', '18 months'),
    ))
    ->add(new Alert(
        content: 'Training revenue was below plan because the Berlin workshop moved into October. No change to the quarterly forecast is required.',
        heading: 'Variance to review',
        variant: AlertVariant::Warning,
    ))
    ->button('Open the complete report', $reportUrl)
    ->text('Prepared for the Northstar leadership team · Figures shown in USD · Internal management use only.')
    ->render();
