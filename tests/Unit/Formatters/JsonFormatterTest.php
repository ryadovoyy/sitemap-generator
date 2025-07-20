<?php

namespace Ryadovoyy\SitemapGenerator\Tests\Unit\Formatters;

use PHPUnit\Framework\TestCase;
use Ryadovoyy\SitemapGenerator\Formatters\JsonFormatter;
use Ryadovoyy\SitemapGenerator\SitemapItem;

class JsonFormatterTest extends TestCase
{
    private JsonFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new JsonFormatter();
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

        $json = $this->formatter->format($items);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals([
            'loc' => 'https://example.com',
            'lastmod' => '2024-03-20',
            'priority' => '0.8',
            'changefreq' => 'daily',
        ], $data[0]);

        $this->assertStringContainsString('    ', $json);
        $this->assertStringContainsString('https://example.com', $json);
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

        $json = $this->formatter->format($items);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals([
            'loc' => 'https://example.com',
        ], $data[0]);
    }

    public function testFormatWithMultipleItems(): void
    {
        $items = [
            new SitemapItem('https://example.com/1', '2024-03-20', '0.8', 'daily'),
            new SitemapItem('https://example.com/2', '2024-03-21', '0.9', 'weekly'),
            new SitemapItem('https://example.com/3', '2024-03-22', '1.0', 'monthly'),
        ];

        $json = $this->formatter->format($items);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertCount(3, $data);

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

        foreach ($data as $index => $item) {
            $this->assertEquals($expectedValues[$index], $item);
        }
    }

    public function testFormatWithEmptyItems(): void
    {
        $json = $this->formatter->format([]);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertEmpty($data);
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

        $json = $this->formatter->format($items);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals(
            'https://example.com/page?id=1&type=test',
            $data[0]['loc']
        );
    }
}
