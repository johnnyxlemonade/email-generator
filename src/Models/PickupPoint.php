<?php

namespace Lemonade\EmailGenerator\Models;

class PickupPoint
{
    /**
     * Konstruktor pro vytvoření instance výdejního místa.
     *
     * @param string|null $id Identifikátor výdejního místa (nepovinný).
     * @param string|null $name Název výdejního místa (nepovinný).
     * @param string|null $street Ulice výdejního místa (nepovinný).
     * @param string|null $city Město výdejního místa (nepovinný).
     * @param array<string, string> $openingHours Otevírací doba ve formátu ['den' => 'čas'] (nepovinné).
     * @param float|null $latitude Zeměpisná šířka výdejního místa (nepovinné).
     * @param float|null $longitude Zeměpisná délka výdejního místa (nepovinné).
     * @param string|null $googleMapKey API klíč pro Google Mapy (nepovinné).
     */
    public function __construct(
        protected readonly ?string $id = null,
        protected readonly ?string $name = null,
        protected readonly ?string $street = null,
        protected readonly ?string $city = null,
        protected readonly array $openingHours = [],
        protected readonly ?float $latitude = null,
        protected readonly ?float $longitude = null,
        protected readonly ?string $googleMapKey = null
    ) {}

    /**
     * Vrací ID výdejního místa.
     *
     * @return string|null Identifikátor výdejního místa, nebo `null`, pokud není nastaven.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Vrací název výdejního místa.
     *
     * @return string|null Název výdejního místa, nebo `null`, pokud není nastaven.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Vrací ulici výdejního místa.
     *
     * @return string|null Ulice výdejního místa, nebo `null`, pokud není nastavena.
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * Vrací město výdejního místa.
     *
     * @return string|null Město výdejního místa, nebo `null`, pokud není nastaveno.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Vrací otevírací dobu výdejního místa.
     *
     * @return array<string, string> Otevírací doba ve formátu ['den' => 'čas'].
     */
    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    /**
     * Vrací zeměpisnou šířku výdejního místa.
     *
     * @return float|null Zeměpisná šířka výdejního místa, nebo `null`, pokud není nastavena.
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * Vrací zeměpisnou délku výdejního místa.
     *
     * @return float|null Zeměpisná délka výdejního místa, nebo `null`, pokud není nastavena.
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * Vrací Google Map API klíč.
     *
     * @return string|null Google Map API klíč, nebo `null`, pokud není nastaven.
     */
    public function getGoogleMapKey(): ?string
    {
        return $this->googleMapKey;
    }

    /**
     * Vrací plnou adresu jako formátovaný řetězec.
     *
     * @return string|null Formátovaná adresa ve tvaru "ulice, město", nebo `null`, pokud nejsou dostupné žádné informace o adrese.
     */
    public function getFullAddress(): ?string
    {
        // Filtrování prázdných částí adresy (pokud ulice nebo město není nastaveno)
        $addressParts = array_filter([$this->street, $this->city]);

        // Pokud je alespoň část adresy dostupná, spojí je do řetězce, jinak vrátí `null`
        return !empty($addressParts) ? implode(', ', $addressParts) : null;
    }

    /**
     * Vrací formátovanou otevírací dobu jako řetězec.
     *
     * @return string|null Formátovaná otevírací doba, nebo `null`, pokud nejsou dostupné žádné informace o otevírací době.
     */
    public function getFormattedOpeningHours(): ?string
    {
        // Pokud otevírací hodiny nejsou definovány, vrátí `null`
        if (empty($this->openingHours)) {
            return null;
        }

        // Formátování otevírací doby pro každý den
        $formattedHours = [];
        foreach ($this->openingHours as $day => $hours) {
            $formattedHours[] = "{$day}: {$hours}";
        }

        // Vrací jednotlivé otevírací doby spojené znakem <br>
        return implode("<br>", $formattedHours);
    }
}

