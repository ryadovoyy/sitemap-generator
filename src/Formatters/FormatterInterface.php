<?php

namespace Ryadovoyy\SitemapGenerator\Formatters;

use Ryadovoyy\SitemapGenerator\SitemapItem;

/**
 * Interface for sitemap formatters that convert sitemap items to specific formats.
 */
interface FormatterInterface
{
    /**
     * Formats sitemap items into a string representation.
     *
     * @param SitemapItem[] $items Array of sitemap items
     * @return string Formatted sitemap content
     */
    public function format(array $items): string;
}
