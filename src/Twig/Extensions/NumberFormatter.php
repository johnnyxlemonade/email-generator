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
     * @param SupportedCurrencies|string|null $suffix The string to be appended after the number (e.g., currency).
     * @param int $decimals The number of decimal places (default is 2).
     * @return string The formatted value (or the original value if it is not an int or float).
     */
    public function formatPrice(

        string|int|float|null $value,
        SupportedCurrencies|string|null $currency = null,
        int $decimals = 2

    ): string {
        if (is_int($value) || is_float($value)) {

            // Format the number with user-defined parameters
            $formattedValue = number_format($value, $decimals, ',', ' ');

            if ($currency !== null) {

                if($currency instanceof SupportedCurrencies) {

                    $symbol = $currency->getSymbol();

                    if ($currency->isPrefix()) {
                        return $symbol . ' ' . $formattedValue;
                    }

                    return $formattedValue . ' ' . $symbol;
                }

                return $formattedValue . ' ' . $currency;
            }

            return $formattedValue;
        }

        // If the value is not a number (or is null), return it as a string unchanged
        return (string) $value;
    }
}
