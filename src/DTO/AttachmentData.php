<?php

namespace Lemonade\EmailGenerator\DTO;

class AttachmentData
{
    /**
     * @param string $name Název přílohy
     * @param string $link URL odkaz na přílohu
     * @param string|null $size Velikost přílohy v bytech (nepovinné)
     * @param string|null $extension Přípona souboru (nepovinné)
     */
    public function __construct(
        public string $name,
        public string $link,
        public ?string $size = null,
        public ?string $extension = null
    ) {}
}