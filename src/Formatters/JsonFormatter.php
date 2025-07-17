<?php

namespace Ryadovoyy\SitemapGenerator\Formatters;

use Ryadovoyy\SitemapGenerator\SitemapItem;

/**
 * Formats sitemap items into JSON format.
 */
class JsonFormatter implements FormatterInterface
{
    public function format(array $items): string
    {
        $data = array_map(function (SitemapItem $item) {
            return array_filter($item->toArray(), function (?string $itemValue) {
                return isset($itemValue);
            });
        }, $items);

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
