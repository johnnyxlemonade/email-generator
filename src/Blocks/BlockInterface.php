<?php

namespace Lemonade\EmailGenerator\Blocks;

use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Twig\Environment;
use Psr\Log\LoggerInterface;

interface BlockInterface
{
    /**
     * Renders the content of the block.
     *
     * @param Environment $twig Twig renderer.
     * @param LoggerInterface $logger Logger.
     * @param SupportedCurrencies $currency Currency
     * @return string Rendered HTML content of the block.
     */
    public function renderBlock(Environment $twig, LoggerInterface $logger, SupportedCurrencies $currency): string;
}
