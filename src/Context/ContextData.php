<?php

namespace Lemonade\EmailGenerator\Context;
use InvalidArgumentException;

class ContextData
{
    private array $data = [];

    /**
     * Konstruktor, který přijímá volitelné pole dat pro inicializaci.
     *
     * @param array $data Asociativní pole (klíč => hodnota) pro inicializaci dat.
     */
    public function __construct(array $data = [])
    {
        // Přidání všech hodnot z asociativního pole do datového pole
        $this->fromArray($data);
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function fromArray(array $data): self
    {

        if(count($data) > 0) {
            foreach ($data as $key => $value) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Nastaví hodnotu pro zadaný klíč.
     *
     * @param string $key Klíč.
     * @param mixed $value Hodnota.
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Vrátí hodnotu pro zadaný klíč nebo null, pokud klíč neexistuje.
     *
     * @param string $key Klíč.
     * @return mixed|null Hodnota pro klíč nebo null.
     */
    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Ověří, že všechny požadované klíče existují a mají hodnotu.
     * Pokud některý klíč chybí nebo má prázdnou hodnotu, vyhodí výjimku.
     *
     * @param array $keys Pole klíčů, které je potřeba ověřit.
     * @throws InvalidArgumentException Pokud některý z klíčů neexistuje nebo má prázdnou hodnotu.
     */
    public function validate(array $keys): void
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->data) || empty($this->data[$key])) {
                throw new InvalidArgumentException("Chybějící nebo prázdný požadovaný klíč: '$key'.");
            }
        }
    }

    /**
     * Vrátí všechna data jako asociativní pole.
     *
     * @return array Asociativní pole všech dat.
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
