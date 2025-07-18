<?php

namespace Ryadovoyy\SitemapGenerator\Formatters;

/**
 * Formats sitemap items into XML format.
 */
class XmlFormatter implements FormatterInterface
{
    private \DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
    }

    public function format(array $items): string
    {
        $urlset = $this->dom->createElement('urlset');
        $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute(
            'xsi:schemaLocation',
            'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
        );

        foreach ($items as $item) {
            $url = $this->dom->createElement('url');

            $this->appendChildToUrl($url, 'loc', $item->getLoc());

            if ($item->getLastmod() !== null) {
                $this->appendChildToUrl($url, 'lastmod', $item->getLastmod());
            }

            if ($item->getPriority() !== null) {
                $this->appendChildToUrl($url, 'priority', $item->getPriority());
            }

            if ($item->getChangefreq() !== null) {
                $this->appendChildToUrl($url, 'changefreq', $item->getChangefreq());
            }

            $urlset->appendChild($url);
        }

        $this->dom->appendChild($urlset);
        return $this->dom->saveXML();
    }

    private function appendChildToUrl(\DOMElement $url, string $childName, string $childValue): void
    {
        $child = $this->dom->createElement($childName);
        $child->appendChild($this->dom->createTextNode($childValue));
        $url->appendChild($child);
    }
}
