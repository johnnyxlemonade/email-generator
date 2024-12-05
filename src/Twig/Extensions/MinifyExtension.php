<?php

namespace Lemonade\EmailGenerator\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MinifyExtension extends AbstractExtension
{
    /**
     * Returns a list of filters available in this extension.
     *
     * @return TwigFilter[] Array of filters added to Twig.
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('minify', [$this, 'minifyHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Minifies HTML content by removing unnecessary spaces, new lines, and other redundant characters.
     *
     * @param string $content HTML content to be minified.
     * @return string Minified HTML content.
     */
    public function minifyHtml(string $content): string
    {

        // Remove all HTML comments
        $content = preg_replace('/<!--.*?-->/s', '', $content);

        // Remove spaces before and after HTML tags (but keep the content inside the tags intact)
        $content = preg_replace('/\s*(<[^>]+>)\s*/', '$1', $content);

        // Define replacement rules to remove unwanted spaces and other redundant characters.
        $replace = [
            // Remove spaces before and after HTML tags
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => '<',
            // Shorten sequences of whitespace; keep new lines for JavaScript importance
            '/([\t ])+/s' => ' ',
            // Remove leading and trailing spaces on lines
            '/^([\t ])+/m' => '',
            '/([\t ])+$/m' => '',
            // Remove simple comments in JS; does not apply to lines containing URLs
            '~//[a-zA-Z0-9 ]+$~m' => '',
            // Remove empty lines
            '/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",
            // Remove empty lines between HTML tags
            '/\>[\r\n\t ]+\</s' => '><',
            // Remove empty lines containing only closing JS block
            '/}[\r\n\t ]+/s' => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s' => '},',
            // Remove new line after function or condition start in JS
            '/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s' => ',{',
            // Remove new line after line end in JS (only in obvious cases)
            '/\),[\r\n\t ]+/s' => '),',
        ];

        // Use preg_replace to replace according to the given rules
        $body = preg_replace(array_keys($replace), array_values($replace), $content);

        // Define HTML tags that can be removed (e.g., closing tags that are redundant)
        $remove = [
            '</option>',
            '</li>',
            '</dt>',
            '</dd>',
            '</tr>',
            '</th>',
            '</td>',
        ];

        // Remove redundant closing HTML tags
        return str_ireplace($remove, '', $body);
    }
}
