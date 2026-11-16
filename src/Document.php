<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\Component\{BulletList, Button, Divider, Group, Heading, Image, Spacer, Table, Text};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * An immutable email document whose fluent methods return independent documents
 */
final readonly class Document
{
    /**
     * @var list<ComponentInterface>
     */
    private array $components;

    /**
     * Creates a document and snapshots its initial content
     *
     * @param list<ComponentInterface> $components
     */
    public function __construct(
        private HtmlRenderer $renderer,
        private string $locale = 'en',
        private string|Translation $title = '',
        private string|Translation $preheader = '',
        private Direction $direction = Direction::Ltr,
        array $components = [],
    ) {
        InputValidator::locale($locale);
        InputValidator::text($title);
        InputValidator::text($preheader);
        $snapshot = [];
        foreach ($components as $component) {
            array_push($snapshot, ...ComponentExpander::expand([$component]));
        }
        $this->components = $snapshot;
    }

    /**
     * Returns a new document with the supplied compositions expanded atomically
     */
    public function add(ComponentInterface ...$components): self
    {
        $expanded = ComponentExpander::expand($components);
        return new self(
            renderer: $this->renderer,
            locale: $this->locale,
            title: $this->title,
            preheader: $this->preheader,
            direction: $this->direction,
            components: [...$this->components, ...$expanded],
        );
    }

    /**
     * Returns the document content in display order
     *
     * @return list<ComponentInterface>
     */
    public function components(): array
    {
        return $this->components;
    }

    /**
     * Returns a new document with a heading appended
     */
    public function heading(string|Translation|RichText $content, int $level = 1): self
    {
        return $this->add(new Heading($content, $level));
    }

    /**
     * Returns a new document with text appended
     */
    public function text(string|Translation|RichText $content): self
    {
        return $this->add(new Text($content));
    }

    /**
     * Returns a new document with an image appended
     */
    public function image(string $url, string|Translation $alt, int $width = 536, ?int $height = null): self
    {
        return $this->add(new Image($url, $alt, $width, $height));
    }

    /**
     * Returns a new document with a button appended
     */
    public function button(string|Translation $label, string $url): self
    {
        return $this->add(new Button($label, $url));
    }

    /**
     * Returns a new document with a list appended
     *
     * @param list<string|Translation|RichText> $items
     */
    public function list(array $items, bool $ordered = false): self
    {
        return $this->add(new BulletList($items, $ordered));
    }

    /**
     * Returns a new document with a data table appended
     *
     * @param list<string|Translation|RichText> $headers
     * @param list<list<string|Translation|RichText>> $rows
     */
    public function table(array $headers, array $rows): self
    {
        return $this->add(new Table($headers, $rows));
    }

    /**
     * Returns a new document with a divider appended
     */
    public function divider(Color $color = new Color('#e2e8f0'), int $thickness = 1): self
    {
        return $this->add(new Divider($color, $thickness));
    }

    /**
     * Returns a new document with a spacer appended
     */
    public function spacer(int $height = 16): self
    {
        return $this->add(new Spacer($height));
    }

    /**
     * Returns a new document with a visual group appended
     */
    public function group(ComponentInterface ...$components): self
    {
        return $this->add(new Group(array_values($components)));
    }

    /**
     * Renders this document as a complete HTML email
     */
    public function render(): string
    {
        return $this->renderer->render($this);
    }

    /**
     * Returns the explicit rendering locale
     */
    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * Returns the document title
     */
    public function title(): string|Translation
    {
        return $this->title;
    }

    /**
     * Returns the inbox preview text
     */
    public function preheader(): string|Translation
    {
        return $this->preheader;
    }

    /**
     * Returns the text direction
     */
    public function direction(): Direction
    {
        return $this->direction;
    }
}
