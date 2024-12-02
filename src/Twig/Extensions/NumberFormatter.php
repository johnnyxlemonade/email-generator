<?php

namespace Lemonade\EmailGenerator\Twig\Extensions;

use Lemonade\EmailGenerator\Localization\SupportedCurrencies;

class NumberFormatter
{
    /**
     * Formátuje číselnou hodnotu na řetězec s pevně daným formátem.
     * Pokud hodnota není int nebo float, vrátí ji nezměněnou.
     *
     * @param string|int|float|null $value Hodnota, která se má formátovat.
     * @param string|null $suffix Řetězec, který se má připojit za číslo (např. měna).
     * @param int $decimals Počet desetinných míst (výchozí hodnota je 2).
     * @return string Formátovaná hodnota (nebo původní hodnota, pokud není typu int nebo float).
     */
    public function format(
        string|int|float|null $value,
        ?string $suffix = null,
        int $decimals = 2
    ): string {
        if (is_int($value) || is_float($value)) {
            // Formátování čísla s uživatelsky definovanými parametry
            $formattedValue = number_format($value, $decimals, ',', ' ');

            if ($suffix !== null) {
                $currencyEnum = SupportedCurrencies::tryFrom(strtoupper($suffix));

                if ($currencyEnum !== null) {
                    $symbol = $currencyEnum->getSymbol();
                    if ($currencyEnum->isPrefix()) {
                        return $symbol . ' ' . $formattedValue;
                    }
                    return $formattedValue . ' ' . $symbol;
                }

                // Pokud měna není podporována v `SupportedCurrencies`
                return $formattedValue . ' ' . $suffix;
            }

            return $formattedValue;
        }

        // Pokud hodnota není číslo (nebo je null), vrátíme ji jako řetězec beze změny
        return (string)$value;
    }
}




