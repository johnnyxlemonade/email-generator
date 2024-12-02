<?php

namespace Lemonade\EmailGenerator\Blocks;

use Twig\Environment;
use Psr\Log\LoggerInterface;

interface BlockInterface
{
    /**
     * Vykreslí obsah bloku.
     *
     * @param Environment $twig Twig renderer.
     * @param LoggerInterface $logger Logger.
     * @return string Vykreslený HTML obsah bloku.
     */
    public function renderBlock(Environment $twig, LoggerInterface $logger): string;
}