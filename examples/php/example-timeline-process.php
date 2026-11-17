<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$projectUrl = 'https://example.com/projects/orion-migration';
$completedBackground = new Color('f0fdf4');
$completedBorder = new Color('86efac');
$currentBackground = new Color('eff6ff');
$currentBorder = new Color('60a5fa');
$pendingBackground = new Color('f8fafc');
$pendingBorder = new Color('cbd5e1');

return $emails->create(
    locale: 'en',
    title: 'Orion migration — rollout timeline update',
    preheader: 'Data migration is complete and the team rollout begins on November 8.',
)->text(new RichText(
    TextRun::strong('ORION MIGRATION'),
    "\nWeekly delivery update · November 3, 2027",
))->divider(new Color('1d4ed8'), thickness: 3)
    ->add(new Badge('On track', BadgeVariant::Info))
    ->heading('The rollout moves to the team phase')
    ->text('The production data migration completed without reconciliation issues. Workspace leads can now prepare their teams for the phased rollout beginning next Monday.')
    ->add(new Steps(
        ['Discovery', 'Data migration', 'Team rollout', 'Review'],
        currentStep: 3,
    ))
    ->heading('Project timeline', level: 2)
    ->add(new Group(
        children: [new Columns(
            first: [
                new Badge('Completed', BadgeVariant::Success),
                new Text('SEP 12–22'),
            ],
            second: [
                new Heading('Discovery and readiness', level: 3),
                new Text('Mapped 14 workflows, confirmed data ownership and agreed the cutover criteria with every regional lead.'),
            ],
            gap: 20,
            firstWidth: 136,
            mobileFirstWidth: 96,
        )],
        backgroundColor: $completedBackground,
        padding: 18,
        borderColor: $completedBorder,
    ))
    ->add(new Group(
        children: [new Columns(
            first: [
                new Badge('Completed', BadgeVariant::Success),
                new Text('OCT 2–27'),
            ],
            second: [
                new Heading('Data migration', level: 3),
                new Text('Migrated 186,240 records, validated permissions and archived the final reconciliation report with no critical exceptions.'),
            ],
            gap: 20,
            firstWidth: 136,
            mobileFirstWidth: 96,
        )],
        backgroundColor: $completedBackground,
        padding: 18,
        borderColor: $completedBorder,
    ))
    ->add(new Group(
        children: [new Columns(
            first: [
                new Badge('Current', BadgeVariant::Info),
                new Text('NOV 8–19'),
            ],
            second: [
                new Heading('Phased team rollout', level: 3),
                new Text(new RichText(
                    TextRun::strong('Next milestone: European teams go live on November 8.'),
                    "\nOffice hours run daily at 14:00 UTC while access is enabled region by region.",
                )),
            ],
            gap: 20,
            firstWidth: 136,
            mobileFirstWidth: 96,
        )],
        backgroundColor: $currentBackground,
        padding: 18,
        borderColor: $currentBorder,
    ))
    ->add(new Group(
        children: [new Columns(
            first: [
                new Badge('Planned', BadgeVariant::Neutral),
                new Text('NOV 22–DEC 3'),
            ],
            second: [
                new Heading('Adoption review', level: 3),
                new Text('Review usage, resolve remaining workflow gaps and hand ownership from the migration team to business operations.'),
            ],
            gap: 20,
            firstWidth: 136,
            mobileFirstWidth: 96,
        )],
        backgroundColor: $pendingBackground,
        padding: 18,
        borderColor: $pendingBorder,
    ))
    ->button('Open the project workspace', $projectUrl)
    ->text('Project owner: Priya Raman · Next update: November 10, 2027')
    ->render();
