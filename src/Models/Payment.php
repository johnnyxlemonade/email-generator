<?php

namespace Lemonade\EmailGenerator\Models;

class Payment
{
    /**
     * @param string $name Název způsobu platby.
     * @param float|int|string|null $price Cena platby (může být různých typů, ale bude konvertována na float).
     * @param bool $display Příznak, zda se má cena platby zobrazovat (výchozí hodnota je `true`).
     */
    public function __construct(
        protected readonly string $name,
        protected readonly float|int|string|null $price,
        protected readonly bool $display = true
    ) {
    }

    /**
     * Vrací název způsobu platby.
     *
     * @return string Název platby.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Vrací cenu platby jako float.
     *
     * @return float|null Vrací cenu platby jako float nebo `null`, pokud není cena k dispozici.
     */
    public function getPrice(): ?float
    {
        return $this->price !== null ? (float) $this->price : null;
    }

    /**
     * Vrací příznak, zda se má cena platby zobrazit.
     *
     * @return bool `true` pokud se má cena zobrazovat, jinak `false`.
     */
    public function shouldDisplay(): bool
    {
        return $this->display;
    }

}
