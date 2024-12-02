<?php

namespace Lemonade\EmailGenerator\Tests\Twig\Extensions;

use Lemonade\EmailGenerator\Twig\Extensions\MinifyExtension;
use PHPUnit\Framework\TestCase;

class MinifyExtensionTest extends TestCase
{
    private MinifyExtension $minifyExtension;

    /**
     * Nastaví testovací prostředí před každým testem.
     */
    protected function setUp(): void
    {
        $this->minifyExtension = new MinifyExtension();
    }

    /**
     * Testuje, že redundantní uzavírací HTML tagy jsou odstraněny a výsledek je správně zhuštěný.
     */
    public function testRemoveRedundantClosingTags(): void
    {
        $inputHtml = "
            <select>
                <option value=\"1\">Option 1</option>
                <option value=\"2\">Option 2</option>
            </select>
            <ul>
                <li>Item 1</li>
                <li>Item 2</li>
            </ul>
        ";

        // Očekávaný výstup bez nadbytečných uzavíracích tagů a bez extra mezer
        $expectedOutput = "<select><option value=\"1\">Option 1<option value=\"2\">Option 2</select><ul><li>Item 1<li>Item 2</ul>";

        $output = $this->minifyExtension->minifyHtml($inputHtml);

        // Odstraníme veškeré počáteční a koncové bílé znaky včetně nových řádků
        $normalizedExpectedOutput = trim($expectedOutput);
        $normalizedOutput = trim($output);

        // Porovnání očekávaného výstupu se skutečným výstupem
        $this->assertEquals($normalizedExpectedOutput, $normalizedOutput);
    }
}
