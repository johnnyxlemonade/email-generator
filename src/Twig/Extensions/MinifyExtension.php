<?php

namespace Lemonade\EmailGenerator\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Třída MinifyExtension slouží jako Twig rozšíření pro minifikaci HTML obsahu.
 * Obsahuje vlastní filtr 'minify', který odstraní zbytečné mezery a nové řádky
 * z HTML výstupu a zajistí tak kompaktnější výsledný kód.
 */
class MinifyExtension extends AbstractExtension
{
    /**
     * Vrací seznam filtrů, které jsou k dispozici v tomto rozšíření.
     *
     * @return TwigFilter[] Pole filtrů přidaných do Twigu.
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('minify', [$this, 'minifyHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Minifikuje HTML obsah tím, že odstraní zbytečné mezery, nové řádky a další
     * nadbytečné znaky.
     *
     * @param string $content HTML obsah, který má být minifikován.
     * @return string Minifikovaný HTML obsah.
     */
    public function minifyHtml(string $content): string
    {
        // Definování pravidel pro nahrazení, která odstraní nežádoucí mezery a jiné zbytečné znaky.
        $replace = [
            // Odstraní mezery před a po HTML tagy
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => '<',
            // Zkrátí posloupnosti bílých znaků; ponechá nové řádky kvůli důležitosti v JavaScriptu
            '/([\t ])+/s' => ' ',
            // Odstraní vedoucí a koncové mezery na řádcích
            '/^([\t ])+/m' => '',
            '/([\t ])+$/m' => '',
            // Odstraní jednoduché komentáře v JS; nevztahuje se na řádky obsahující URL
            '~//[a-zA-Z0-9 ]+$~m' => '',
            // Odstraní prázdné řádky
            '/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",
            // Odstraní prázdné řádky mezi HTML tagy
            '/\>[\r\n\t ]+\</s' => '><',
            // Odstraní prázdné řádky obsahující pouze uzavírací znak bloku JS
            '/}[\r\n\t ]+/s' => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s' => '},',
            // Odstraní nový řádek po začátku funkce nebo podmínky v JS
            '/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s' => ',{',
            // Odstraní nový řádek po ukončení řádku v JS (pouze v zřejmých případech)
            '/\),[\r\n\t ]+/s' => '),',
        ];

        // Použití preg_replace pro nahrazení dle zadaných pravidel
        $body = preg_replace(array_keys($replace), array_values($replace), $content);

        // Definování HTML tagů, které lze odstranit (např. uzavírací tagy, které jsou redundantní)
        $remove = [
            '</option>',
            '</li>',
            '</dt>',
            '</dd>',
            '</tr>',
            '</th>',
            '</td>',
        ];

        // Odstranění redundantních uzavíracích HTML tagů
        return str_ireplace($remove, '', $body);
    }
}
