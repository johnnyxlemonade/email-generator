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
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;

/** @var EmailGenerator $emails */

$calendarUrl = 'https://example.com/appointments/APT-2048/calendar';
$manageUrl = 'https://example.com/appointments/APT-2048';

return $emails->create(
    locale: 'en',
    title: 'Your appointment has been rescheduled',
    preheader: 'Your consultation now takes place on Thursday, November 11 at 14:30.',
)->add(new Badge('Schedule updated', BadgeVariant::Warning))
    ->heading('Your appointment has a new time')
    ->text('Hello Jamie, Dr Lewis needed to adjust the schedule. Your appointment is confirmed for the new time below; the location and preparation instructions are unchanged.')
    ->add(new Columns(
        first: [new Group(
            children: [
                new Badge('Previous', BadgeVariant::Neutral),
                new Heading('Tuesday, Nov 9', level: 3),
                new Text('10:00–10:45'),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 20,
            borderColor: new Color('e2e8f0'),
        )],
        second: [new Group(
            children: [
                new Badge('Confirmed', BadgeVariant::Success),
                new Heading('Thursday, Nov 11', level: 3),
                new Text(new RichText(TextRun::strong('14:30–15:15'))),
            ],
            backgroundColor: new Color('f0fdf4'),
            padding: 20,
            borderColor: new Color('86efac'),
        )],
        gap: 20,
    ))
    ->add(KeyValue::listing(
        new KeyValue('Appointment', 'Follow-up consultation'),
        new KeyValue('Clinician', 'Dr Amelia Lewis'),
        new KeyValue('Location', 'Riverside Clinic · Room 204'),
        new KeyValue('Reference', 'APT-2048'),
    ))
    ->add(new ButtonGroup([
        new Button('Update my calendar', $calendarUrl),
        new Button('Manage appointment', $manageUrl, ButtonVariant::Secondary),
    ]))
    ->add(new Alert(
        content: 'Please arrive 10 minutes early and bring the medication list discussed at your last visit.',
        heading: 'Preparation is unchanged',
        variant: AlertVariant::Info,
    ))
    ->text('If the new time does not work, use the manage appointment link or call the clinic before November 10.')
    ->divider()
    ->add(new Signature(new Contact(
        name: 'Sophie Grant',
        position: 'Patient Coordinator',
        organization: 'Riverside Clinic',
        email: 'appointments@example.com',
        phone: '+1 415 555 0184',
    )))
    ->render();
