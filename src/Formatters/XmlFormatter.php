<?php

namespace Ryadovoyy\SitemapGenerator\Formatters;

/**
 * Formats sitemap items into XML format.
 */
class XmlFormatter implements FormatterInterface
{
    public function format(array $items): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute(
            'xsi:schemaLocation',
            'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
        );

        foreach ($items as $item) {
            $url = $dom->createElement('url');

            $loc = $dom->createElement('loc', $item->getLoc());
            $url->appendChild($loc);

            if ($item->getLastmod() !== null) {
                $lastmod = $dom->createElement('lastmod', $item->getLastmod());
                $url->appendChild($lastmod);
            }

            if ($item->getPriority() !== null) {
                $priority = $dom->createElement('priority', $item->getPriority());
                $url->appendChild($priority);
            }

            if ($item->getChangefreq() !== null) {
                $changefreq = $dom->createElement('changefreq', $item->getChangefreq());
                $url->appendChild($changefreq);
            }

            $urlset->appendChild($url);
        }

        $dom->appendChild($urlset);
        return $dom->saveXML();
    }
}
