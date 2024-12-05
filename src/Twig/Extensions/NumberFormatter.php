<?php

namespace Lemonade\EmailGenerator\Twig\Extensions;

use Lemonade\EmailGenerator\Localization\SupportedCurrencies;

class NumberFormatter
{
    /**
     * Formats a numeric value to a string with a fixed format.
     * If the value is not an int or float, it returns it unchanged.
     *
     * @param string|int|float|null $value The value to be formatted.
     * @param string|null $suffix The string to be appended after the number (e.g., currency).
     * @param int $decimals The number of decimal places (default is 2).
     * @return string The formatted value (or the original value if it is not an int or float).
     */
    public function format(
        string|int|float|null $value,
        ?string $suffix = null,
        int $decimals = 2
    ): string {
        if (is_int($value) || is_float($value)) {
            // Format the number with user-defined parameters
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

                // If the currency is not supported in `SupportedCurrencies`
                return $formattedValue . ' ' . $suffix;
            }

            return $formattedValue;
        }

        // If the value is not a number (or is null), return it as a string unchanged
        return (string)$value;
    }
}
