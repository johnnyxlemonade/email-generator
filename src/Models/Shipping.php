<?php

namespace Lemonade\EmailGenerator\Models;

class Shipping
{
    /**
     * @param string $name Název způsobu dopravy.
     * @param float|int|string|null $price Cena dopravy (může být různých typů, ale bude konvertována na float).
     * @param bool $display Příznak, zda se má cena dopravy zobrazovat (výchozí hodnota je `true`).
     */
    public function __construct(
        protected readonly string $name,
        protected readonly float|int|string|null $price,
        protected readonly bool $display = true
    ) {
    }

    /**
     * Vrací název způsobu dopravy.
     *
     * @return string Název dopravy.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Vrací cenu dopravy jako float.
     *
     * @return float|null Vrací cenu dopravy jako float nebo `null`, pokud není cena k dispozici.
     */
    public function getPrice(): ?float
    {
        return $this->price !== null ? (float) $this->price : null;
    }

    /**
     * Vrací příznak, zda se má cena dopravy zobrazit.
     *
     * @return bool `true` pokud se má cena zobrazovat, jinak `false`.
     */
    public function shouldDisplay(): bool
    {
        return $this->display;
    }
}
