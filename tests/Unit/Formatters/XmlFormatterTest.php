<?php

namespace Ryadovoyy\SitemapGenerator\Tests\Unit\Formatters;

use PHPUnit\Framework\TestCase;
use Ryadovoyy\SitemapGenerator\Formatters\XmlFormatter;
use Ryadovoyy\SitemapGenerator\SitemapItem;

class XmlFormatterTest extends TestCase
{
    private XmlFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new XmlFormatter();
    }

    public function testFormatWithAllFields(): void
    {
        $items = [
            new SitemapItem(
                'https://example.com',
                '2024-03-20',
                '0.8',
                'daily'
            ),
        ];

        $xml = $this->formatter->format($items);

        $this->assertIsString($xml);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $urlset = $doc->documentElement;
        $this->assertEquals('urlset', $urlset->tagName);
        $this->assertEquals(
            'http://www.sitemaps.org/schemas/sitemap/0.9',
            $urlset->getAttribute('xmlns')
        );
        $this->assertEquals(
            'http://www.w3.org/2001/XMLSchema-instance',
            $urlset->getAttribute('xmlns:xsi')
        );
        $this->assertEquals(
            'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd',
            $urlset->getAttribute('xsi:schemaLocation')
        );

        $urls = $urlset->getElementsByTagName('url');
        $this->assertEquals(1, $urls->length);

        $url = $urls->item(0);
        $this->assertEquals(
            'https://example.com',
            $url->getElementsByTagName('loc')->item(0)->textContent
        );
        $this->assertEquals(
            '2024-03-20',
            $url->getElementsByTagName('lastmod')->item(0)->textContent
        );
        $this->assertEquals(
            '0.8',
            $url->getElementsByTagName('priority')->item(0)->textContent
        );
        $this->assertEquals(
            'daily',
            $url->getElementsByTagName('changefreq')->item(0)->textContent
        );
    }

    public function testFormatWithOptionalFieldsNull(): void
    {
        $items = [
            new SitemapItem(
                'https://example.com',
                null,
                null,
                null
            ),
        ];

        $xml = $this->formatter->format($items);
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $url = $doc->getElementsByTagName('url')->item(0);

        $this->assertEquals(1, $url->getElementsByTagName('loc')->length);
        $this->assertEquals(0, $url->getElementsByTagName('lastmod')->length);
        $this->assertEquals(0, $url->getElementsByTagName('priority')->length);
        $this->assertEquals(0, $url->getElementsByTagName('changefreq')->length);
    }

    public function testFormatWithMultipleItems(): void
    {
        $items = [
            new SitemapItem('https://example.com/1', '2024-03-20', '0.8', 'daily'),
            new SitemapItem('https://example.com/2', '2024-03-21', '0.9', 'weekly'),
            new SitemapItem('https://example.com/3', '2024-03-22', '1.0', 'monthly'),
        ];

        $xml = $this->formatter->format($items);
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $urls = $doc->getElementsByTagName('url');
        $this->assertEquals(3, $urls->length);

        $expectedValues = [
            [
                'loc' => 'https://example.com/1',
                'lastmod' => '2024-03-20',
                'priority' => '0.8',
                'changefreq' => 'daily',
            ],
            [
                'loc' => 'https://example.com/2',
                'lastmod' => '2024-03-21',
                'priority' => '0.9',
                'changefreq' => 'weekly',
            ],
            [
                'loc' => 'https://example.com/3',
                'lastmod' => '2024-03-22',
                'priority' => '1.0',
                'changefreq' => 'monthly',
            ],
        ];

        for ($i = 0; $i < 3; $i++) {
            $url = $urls->item($i);
            $this->assertEquals(
                $expectedValues[$i]['loc'],
                $url->getElementsByTagName('loc')->item(0)->textContent
            );
            $this->assertEquals(
                $expectedValues[$i]['lastmod'],
                $url->getElementsByTagName('lastmod')->item(0)->textContent
            );
            $this->assertEquals(
                $expectedValues[$i]['priority'],
                $url->getElementsByTagName('priority')->item(0)->textContent
            );
            $this->assertEquals(
                $expectedValues[$i]['changefreq'],
                $url->getElementsByTagName('changefreq')->item(0)->textContent
            );
        }
    }

    public function testFormatWithEmptyItems(): void
    {
        $xml = $this->formatter->format([]);
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $urlset = $doc->documentElement;
        $this->assertEquals('urlset', $urlset->tagName);
        $this->assertEquals(0, $urlset->getElementsByTagName('url')->length);
    }

    public function testFormatWithSpecialCharactersInUrl(): void
    {
        $items = [
            new SitemapItem(
                'https://example.com/page?id=1&type=test',
                '2024-03-20',
                '0.8',
                'daily'
            ),
        ];

        $xml = $this->formatter->format($items);
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $url = $doc->getElementsByTagName('url')->item(0);

        $this->assertEquals(
            'https://example.com/page?id=1&type=test',
            $url->getElementsByTagName('loc')->item(0)->textContent
        );

        $this->assertStringContainsString('&amp;', $xml);
    }
}
