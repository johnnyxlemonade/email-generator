<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;

/**
 * Verifies localized, escaped and linked attachment-list rendering
 */
final class AttachmentListRenderingTest extends EmailTestCase
{
    public function testFilesRenderAsEscapedRowsWithOnlyExplicitLinks(): void
    {
        $html = $this->emails->create(locale: 'en')->add(new AttachmentList([
            new AttachmentItem(
                'Contract <final>.pdf',
                1_800_000,
                'https://example.com/files/contract.pdf?download=1&source=email',
            ),
            new AttachmentItem('Price-offer.docx'),
        ]))->render();
        $xpath = $this->xpath($html);

        self::assertStringContainsString('Attached documents — 2 files', $html);
        self::assertStringContainsString('PDF', $html);
        self::assertStringContainsString('1.8 MB', $html);
        self::assertStringContainsString('DOCX', $html);
        self::assertSame(1.0, $xpath->evaluate('count(//a)'));
        self::assertSame('Contract <final>.pdf', $xpath->evaluate('string(//a)'));
        self::assertSame(
            'https://example.com/files/contract.pdf?download=1&source=email',
            $xpath->evaluate('string(//a/@href)'),
        );
        self::assertSame(0.0, $xpath->evaluate('count(//script)'));
        self::assertStringContainsString('Price-offer.docx', $html);
    }

    public function testSingleFileHeadingUsesTheDocumentLocale(): void
    {
        $html = $this->emails->create(locale: 'cs')
            ->add(new AttachmentList([new AttachmentItem('Smlouva.pdf', 0)]))
            ->render();

        self::assertStringContainsString('Přiložený dokument — 1 soubor', $html);
        self::assertStringContainsString('PDF', $html);
        self::assertStringContainsString('0 B', $html);
    }

    public function testEmptyListDoesNotAddVisibleMarkup(): void
    {
        $empty = $this->emails->create();

        self::assertSame($empty->render(), $empty->add(new AttachmentList([]))->render());
    }
}
