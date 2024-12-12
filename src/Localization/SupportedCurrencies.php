<?php

namespace Lemonade\EmailGenerator\Localization;

enum SupportedCurrencies: string
{
    case CZK = 'CZK'; // Czech Republic
    case EUR = 'EUR'; // Eurozone
    case USD = 'USD'; // United States
    case GBP = 'GBP'; // United Kingdom
    case CHF = 'CHF'; // Switzerland
    case RUB = 'RUB'; // Russia
    case PLN = 'PLN'; // Poland
    case JPY = 'JPY'; // Japan
    case AUD = 'AUD'; // Australia
    case CAD = 'CAD'; // Canada
    case SEK = 'SEK'; // Sweden
    case NOK = 'NOK'; // Norway
    case DKK = 'DKK'; // Denmark
    case HUF = 'HUF'; // Hungary
    case RON = 'RON'; // Romania
    case TRY = 'TRY'; // Turkey
    case BGN = 'BGN'; // Bulgaria
    case HRK = 'HRK'; // Croatia
    case CNY = 'CNY'; // China
    case INR = 'INR'; // India
    case KRW = 'KRW'; // South Korea
    case MXN = 'MXN'; // Mexico
    case ZAR = 'ZAR'; // South Africa
    case BRL = 'BRL'; // Brazil
    case SGD = 'SGD'; // Singapore
    case HKD = 'HKD'; // Hong Kong
    case NZD = 'NZD'; // New Zealand
    case THB = 'THB'; // Thailand
    case MYR = 'MYR'; // Malaysia
    case PHP = 'PHP'; // Philippines
    case IDR = 'IDR'; // Indonesia
    case ARS = 'ARS'; // Argentina
    case COP = 'COP'; // Colombia
    case CLP = 'CLP'; // Chile
    case SAR = 'SAR'; // Saudi Arabia
    case AED = 'AED'; // United Arab Emirates
    case KWD = 'KWD'; // Kuwait
    case ILS = 'ILS'; // Israel
    case TWD = 'TWD'; // Taiwan
    case PKR = 'PKR'; // Pakistan
    case NGN = 'NGN'; // Nigeria
    case UAH = 'UAH'; // Ukraine
    case VND = 'VND'; // Vietnam
    case BHD = 'BHD'; // Bahrain
    case QAR = 'QAR'; // Qatar
    case LKR = 'LKR'; // Sri Lanka
    case OMR = 'OMR'; // Oman
    case KES = 'KES'; // Kenya
    case GHS = 'GHS'; // Ghana
    case PEN = 'PEN'; // Peru
    case EGP = 'EGP'; // Egypt

    /**
     * Retrieves the symbol for the currency.
     *
     * @return string The currency symbol.
     */
    public function getSymbol(): string
    {
        return match ($this) {
            self::CZK => 'Kč',
            self::EUR => '€',
            self::USD => '$',
            self::GBP => '£',
            self::CHF => '₣',
            self::RUB => '₽',
            self::PLN => 'zł',
            self::JPY => '¥',
            self::AUD => 'A$',
            self::CAD => 'C$',
            self::SEK => 'kr',
            self::NOK => 'kr',
            self::DKK => 'kr',
            self::HUF => 'Ft',
            self::RON => 'lei',
            self::TRY => '₺',
            self::BGN => 'лв',
            self::HRK => 'kn',
            self::CNY => '¥',
            self::INR => '₹',
            self::KRW => '₩',
            self::MXN => 'Mex$',
            self::ZAR => 'R',
            self::BRL => 'R$',
            self::SGD => 'S$',
            self::HKD => 'HK$',
            self::NZD => 'NZ$',
            self::THB => '฿',
            self::MYR => 'RM',
            self::PHP => '₱',
            self::IDR => 'Rp',
            self::ARS => 'AR$',
            self::COP => 'COP$',
            self::CLP => 'CLP$',
            self::KWD => 'KD',
            self::ILS => '₪',
            self::TWD => 'NT$',
            self::PKR => '₨',
            self::NGN => '₦',
            self::UAH => '₴',
            self::VND => '₫',
            self::BHD => 'BD',
            self::QAR => 'QR',
            self::LKR => '₨',
            self::OMR => '﷼',
            self::KES => 'KSh',
            self::GHS => '₵',
            self::EGP => 'E£',
            default => $this->value
        };
    }

    /**
     * Determines if the currency symbol should be used as a prefix.
     *
     * @return bool True if the symbol should be used as a prefix, false otherwise.
     */
    public function isPrefix(): bool
    {
        return match ($this) {
            self::EUR, self::USD, self::GBP, self::CHF, self::JPY, self::KRW, self::BRL, self::SGD, self::HKD, self::NZD => true,
            default => false,
        };
    }
}

