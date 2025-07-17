<?php

namespace Ryadovoyy\SitemapGenerator\Formatters;

/**
 * Formats sitemap items into CSV format with semicolon delimiters.
 */
class CsvFormatter implements FormatterInterface
{
    public function format(array $items): string
    {
        $output = "loc;lastmod;priority;changefreq\n";

        foreach ($items as $item) {
            $output .= sprintf(
                "%s;%s;%s;%s\n",
                $item->getLoc(),
                $item->getLastmod(),
                $item->getPriority(),
                $item->getChangefreq()
            );
        }

        return $output;
    }
}
