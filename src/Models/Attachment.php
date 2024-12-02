<?php

namespace Lemonade\EmailGenerator\Models;

class Attachment
{
    /**
     * @param string $name Název přílohy
     * @param string $link URL odkaz na přílohu
     * @param string|null $size Velikost přílohy v bytech (nepovinné)
     * @param string|null $extension Přípona souboru (nepovinné)
     */
    public function __construct(
        protected readonly string $name,
        protected readonly string $link,
        protected readonly ?string $size = null,
        protected readonly ?string $extension = null
    ) {}

    /**
     * Vrací odkaz na přílohu.
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Vrací název přílohy.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Vrací velikost přílohy.
     *
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * Vrací příponu souboru.
     *
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function getDescription(): string
    {

        $data = [];

        if($this->extension !== null) {

            $data[] = $this->extension;
        }

        if($this->size !== null) {

            $data[] = $this->size;
        }

        return implode(', ', $data);
    }
}