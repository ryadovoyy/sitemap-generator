<?php

namespace Ryadovoyy\SitemapGenerator\Tests\Unit\Formatters;

use PHPUnit\Framework\TestCase;
use Ryadovoyy\SitemapGenerator\Formatters\CsvFormatter;
use Ryadovoyy\SitemapGenerator\SitemapItem;

class CsvFormatterTest extends TestCase
{
    private CsvFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new CsvFormatter();
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

        $csv = $this->formatter->format($items);
        $lines = explode("\n", trim($csv));

        $this->assertEquals('loc;lastmod;priority;changefreq', $lines[0]);
        $this->assertEquals('https://example.com;2024-03-20;0.8;daily', $lines[1]);
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

        $csv = $this->formatter->format($items);
        $lines = explode("\n", trim($csv));

        $this->assertEquals('loc;lastmod;priority;changefreq', $lines[0]);
        $this->assertEquals('https://example.com;;;', $lines[1]);
    }

    public function testFormatWithMultipleItems(): void
    {
        $items = [
            new SitemapItem('https://example.com/1', '2024-03-20', '0.8', 'daily'),
            new SitemapItem('https://example.com/2', '2024-03-21', '0.9', 'weekly'),
            new SitemapItem('https://example.com/3', '2024-03-22', '1.0', 'monthly'),
        ];

        $csv = $this->formatter->format($items);
        $lines = explode("\n", trim($csv));

        $this->assertEquals('loc;lastmod;priority;changefreq', $lines[0]);

        $expectedLines = [
            'https://example.com/1;2024-03-20;0.8;daily',
            'https://example.com/2;2024-03-21;0.9;weekly',
            'https://example.com/3;2024-03-22;1.0;monthly',
        ];

        foreach ($expectedLines as $index => $expectedLine) {
            $this->assertEquals($expectedLine, $lines[$index + 1]);
        }
    }

    public function testFormatWithEmptyItems(): void
    {
        $csv = $this->formatter->format([]);
        $lines = explode("\n", trim($csv));

        $this->assertEquals('loc;lastmod;priority;changefreq', $lines[0]);
        $this->assertCount(1, $lines);
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

        $csv = $this->formatter->format($items);
        $lines = explode("\n", trim($csv));

        $this->assertEquals('loc;lastmod;priority;changefreq', $lines[0]);
        $this->assertEquals(
            'https://example.com/page?id=1&type=test;2024-03-20;0.8;daily',
            $lines[1]
        );
    }
}
