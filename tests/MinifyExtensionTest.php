<?php

use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\Twig\Extensions\MinifyExtension;

class MinifyExtensionTest extends TestCase
{
    private MinifyExtension $minifyExtension;

    protected function setUp(): void
    {
        $this->minifyExtension = new MinifyExtension();
    }

    /**
     * Test minification of basic HTML.
     */
    public function testMinifyHtml(): void
    {
        $input = "<div>   Hello World!   </div>";
        $expected = "<div>Hello World!</div>";
        $output = $this->minifyExtension->minifyHtml($input);
        $this->assertEquals($expected, $output);
    }

    /**
     * Test minification with multiple whitespace characters.
     */
    public function testMinifyHtmlWithSpaces(): void
    {
        $input = "<div>\n     <p>   This is    a   test.    </p>\n</div>";
        $expected = "<div><p>This is a test.</p></div>";
        $output = $this->minifyExtension->minifyHtml($input);
        $this->assertEquals($expected, $output);
    }

    /**
     * Test minification with comments.
     */
    public function testMinifyHtmlWithComments(): void
    {
        $input = "<div> <!-- Comment here --> <p>Content</p> </div>";
        $expected = "<div><p>Content</p></div>";
        $output = $this->minifyExtension->minifyHtml($input);
        $this->assertEquals($expected, $output);
    }


    /**
     * Test edge case with special characters.
     */
    public function testMinifyHtmlWithSpecialChars(): void
    {
        $input = "<div> &lt;div&gt;Hello &amp; welcome&lt;/div&gt; </div>";
        $expected = "<div>&lt;div&gt;Hello &amp; welcome&lt;/div&gt;</div>";
        $output = $this->minifyExtension->minifyHtml($input);
        $this->assertEquals($expected, $output);
    }

    public function testMinifyHtmlRemovesCommentsAndPreservesInlineCss()
    {
        $html = '<div style="color: blue;">Hello<!-- comment --> World!</div>';
        $expected = '<div style="color: blue;">Hello World!</div>';

        $this->assertEquals($expected, $this->minifyExtension->minifyHtml($html));
    }
}
