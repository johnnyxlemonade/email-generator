<?php

namespace Lemonade\EmailGenerator\Localization;

class ReferenceDictionary
{
    /**
     * Reference dictionary of keys with default Czech translations.
     *
     * @var array
     */
    private static array $keys = [
        "addressLabel" => "Zákaznická data",
        "addressDescription" => "fakturační a doručovací adresa",
        "addressBlockBillingLabel" => "Fakturační adresa",
        "addressBlockDeliveryLabel" => "Doručovací adresa",
        "attachmentListLabel" => "Přílohy",
        "attachmentListDescription" => "byly připojeny, které lze stáhnout",
        "eccomerceMessageLabel" => "Poznámka",
        "eccomerceMessageDescription" => "zákaznická poznámka k objednávce",
        "eccomerceMessageNoMessage" => "Zákazník neuvedl žádnou poznámku k objednávce.",
        "deliveryLabel" => "Doprava a platba",
        "deliveryDescription" => "vybraný způsob dopravy a platby",
        "deliveryShippingLabel" => "Způsob doručení",
        "deliveryPaymentLabel" => "Způsob úhrady",
        "deliveryNeedConfirmation" => "bude upřesněno",
        "accountShortLabel" => "ČÚ",
        "answerDesc" => "informace poskytnuté naším pracovníkem",
        "answerName" => "Odpověď",
        "billingAddress" => "Fakturační adresa",
        "buyLabel" => "Zakoupeno",
        "deliveryAddress" => "Doručovací adresa",
        "dic" => "DIČ",
        "greeting" => "S pozdravem",
        "greetingCustomer" => "Dobrý den,",
        "greetingFooter" => "S pozdravem",
        "greetingHeader" => "Dobrý den",
        "goodLabel" => "Zboží (s DPH)",
        "goodLabelBase" => "Zboží (bez DPH)",
        "goodType" => "Zakoupené zboží",
        "ibanLabel" => "IBAN",
        "ic" => "IČO",
        "lostPassHead" => "obdrželi jsme žádost o vytvoření nového hesla pro přihlášení k webu {{webName}}. Pokud si přejete vygenerovat nové heslo, klikněte na odkaz pro změnu hesla.",
        "lostPassText" => "V případě, že heslo měnit nechcete, tento email ignorujte. Vaše heslo zůstalo nezměněno.",
        "oneTimeText" => "obdrželi jsme žádost o vytvoření jednorázového přístupu pro přihlášení k administraci webu {{webName}}. Pokud se chcete přihlásit, pokračujte kliknutím na tlačítko.",
        "orderBankAccountAction" => "Opište si prosím pečlivě identifikační údaje, pomocí kterých lze uhradit Vaši objednávku.",
        "orderCode" => "Kód objednávky",
        "orderCreateMessage" => "Děkujeme za projevenou důvěru při nákupu u nás. Níže naleznete rekapitulaci Vaší objednávky.",
        "orderCreateNotify" => "{{webName}} eviduje novou objednávku zboží. Základní informace zasíláme v tomto emailu. \nInformace o kompletní objednávce naleznete ve své administraci.",
        "orderDate" => "Datum vytvoření",
        "orderLabel" => "Objednávka",
        "orderNumber" => "Číslo objednávky",
        "orderDescription" => "identifikační údaje objednávky",
        "orderLinkStatus" => "Sledování stavu objednávky",
        "orderProductList" => "Produkty",
        "orderProductListDescription" => "zakoupené zboží",
        "orderStatusMessage" => "Informace o stavu objednávky si můžete zkontrolovat na níže uvedené adrese.",
        "orderTotal" => "Celkem k úhradě",
        "paymentLabel" => "Platba",
        "paymentType" => "Způsob platby",
        "questionDesc" => "informace o Vašem dotazu",
        "questionName" => "Dotaz",
        "quantityLabel" => "Počet",
        "shippingLabel" => "Doprava",
        "shippingType" => "Způsob dopravy",
        "total" => "Celkem",
        "unitPrice" => "Jedn. cena",
        "vsLabel" => "Variabilní symbol"
    ];

    /**
     * Retrieves all reference keys.
     *
     * @return array List of all keys.
     */
    public static function getKeys(): array
    {
        return array_keys(self::$keys);
    }

    /**
     * Retrieves the entire reference dictionary.
     *
     * @return array Associative array with keys and default Czech translations.
     */
    public static function getDictionary(): array
    {
        return self::$keys;
    }

    /**
     * Retrieves the default translation for a given key.
     *
     * @param string $key The key of the phrase.
     * @return string|null The default translation or null if the key does not exist.
     */
    public static function getDefaultTranslation(string $key): ?string
    {
        return self::$keys[$key] ?? null;
    }

}
