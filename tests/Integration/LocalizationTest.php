<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Verifies explicit document locales and framework translation fallback behavior
 */
final class LocalizationTest extends EmailTestCase
{
    public function testInterleavedDocumentsDoNotChangeSharedTranslatorLocale(): void
    {
        $this->translator()->setLocale('de');
        $shared = new Group([new Heading(new Translation('emails.welcome', ['name' => 'Jan']))]);
        $cs = $this->emails()->create(locale: 'cs')->add($shared);
        $en = $this->emails()->create(locale: 'en')->add($shared);
        self::assertStringContainsString('Vítejte, Jan!', $cs->render());
        self::assertStringContainsString('Welcome, Jan!', $en->render());
        self::assertStringContainsString('Vítejte, Jan!', $cs->render());
        self::assertSame('de', $this->translator()->locale());
        self::assertSame('cs', $this->emails()->create()->locale());
    }

    public function testFrameworkFallbackAndMissingKeysArePreserved(): void
    {
        $html = $this->emails()->create(locale: 'cs')->text(new Translation('emails.fallback'))
            ->text(new Translation('emails.missing'))->render();
        self::assertStringContainsString('English fallback', $html);
        self::assertStringContainsString('emails.missing', $html);
        self::assertStringContainsString('English fallback', $this->emails()->create(locale: 'fr')->text(new Translation('emails.fallback'))->render());
    }

    public function testLiteralStringsAreNeverImplicitlyTranslated(): void
    {
        self::assertStringContainsString('emails.activate', $this->emails()->create()->text('emails.activate')->render());
        self::assertStringContainsString('Aktivovat účet', $this->emails()->create()->text(new Translation('emails.activate'))->render());
    }
}
