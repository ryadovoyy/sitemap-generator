<?php

namespace Ryadovoyy\SitemapGenerator\Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;
use Ryadovoyy\SitemapGenerator\Exceptions\InvalidDataException;
use Ryadovoyy\SitemapGenerator\Validators\PageValidator;

class PageValidatorTest extends TestCase
{
    private PageValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PageValidator();
    }

    public function testValidateWithValidData(): void
    {
        $pages = [
            [
                'loc' => 'https://example.com',
                'lastmod' => '2024-03-20',
                'priority' => '0.8',
                'changefreq' => 'daily',
            ],
        ];

        $this->validator->validate($pages);
        $this->assertTrue(true);
    }

    public function testValidateWithEmptyPages(): void
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Pages array cannot be empty');

        $this->validator->validate([]);
    }

    public function testValidateWithTooManyPages(): void
    {
        $page = [
            'loc' => 'https://example.com',
            'lastmod' => '2024-03-20',
            'priority' => '0.8',
            'changefreq' => 'daily',
        ];

        $pages = array_fill(0, 50001, $page);

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Pages array cannot contain more than 50,000 elements');

        $this->validator->validate($pages);
    }

    public function testValidateWithMissingFields(): void
    {
        $pages = [
            [
                'loc' => 'https://example.com',
                'lastmod' => '2024-03-20',
            ],
        ];

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Each page must contain loc, lastmod, priority and changefreq keys');

        $this->validator->validate($pages);
    }

    public function testValidateWithInvalidUrl(): void
    {
        $pages = [
            [
                'loc' => 'not-a-url',
                'lastmod' => '2024-03-20',
                'priority' => '0.8',
                'changefreq' => 'daily',
            ],
        ];

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage("Invalid URL: 'not-a-url'");

        $this->validator->validate($pages);
    }

    public function testValidateWithTooLongUrl(): void
    {
        $pages = [
            [
                'loc' => 'https://example.com/' . str_repeat('a', 2048),
                'lastmod' => '2024-03-20',
                'priority' => '0.8',
                'changefreq' => 'daily',
            ],
        ];

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('URL must be at most 2,048 characters');

        $this->validator->validate($pages);
    }

    public function testValidateWithUnescapedCharactersInUrl(): void
    {
        $unescapedChars = [
            '&' => '&amp;',
            "'" => '&apos;',
            '"' => '&quot;',
            '>' => '&gt;',
            '<' => '&lt;',
        ];

        foreach ($unescapedChars as $char => $entity) {
            $pages = [
                [
                    'loc' => "https://example.com/page{$char}test",
                    'lastmod' => '2024-03-20',
                    'priority' => '0.8',
                    'changefreq' => 'daily',
                ],
            ];

            try {
                $this->validator->validate($pages);
                $this->fail("Expected InvalidDataException for unescaped character '{$char}'");
            } catch (InvalidDataException $e) {
                $this->assertStringContainsString(
                    "URL contains unescaped '{$char}' character. It must be escaped as '{$entity}'",
                    $e->getMessage()
                );
            }
        }
    }

    public function testValidateWithInvalidLastModDate(): void
    {
        $invalidDates = [
            '2024-13-20',
            '2024-02-30',
            '2024/03/20',
            'not-a-date',
            '20-03-2024',
            '2024-3-20',
            '2024-03-2',
        ];

        foreach ($invalidDates as $date) {
            $pages = [
                [
                    'loc' => 'https://example.com',
                    'lastmod' => $date,
                    'priority' => '0.8',
                    'changefreq' => 'daily',
                ],
            ];

            try {
                $this->validator->validate($pages);
                $this->fail("Expected InvalidDataException for invalid date '{$date}'");
            } catch (InvalidDataException $e) {
                $this->assertStringContainsString(
                    'Last modification date must be a valid date string in the W3C Datetime format (YYYY-MM-DD)',
                    $e->getMessage()
                );
            }
        }
    }

    public function testValidateWithInvalidPriority(): void
    {
        $invalidPriorities = [
            '1.1',
            '-0.1',
            '0.11',
            'not-a-number',
            '0.8.0',
            0.8,
            '',
        ];

        foreach ($invalidPriorities as $priority) {
            $pages = [
                [
                    'loc' => 'https://example.com',
                    'lastmod' => '2024-03-20',
                    'priority' => $priority,
                    'changefreq' => 'daily',
                ],
            ];

            try {
                $this->validator->validate($pages);
                $this->fail("Expected InvalidDataException for invalid priority '{$priority}'");
            } catch (InvalidDataException $e) {
                $this->assertStringContainsString(
                    'Priority must be a string between 0.0 and 1.0',
                    $e->getMessage()
                );
            }
        }
    }

    public function testValidateWithInvalidChangeFrequency(): void
    {
        $pages = [
            [
                'loc' => 'https://example.com',
                'lastmod' => '2024-03-20',
                'priority' => '0.8',
                'changefreq' => 'invalid-frequency',
            ],
        ];

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage("Invalid change frequency: 'invalid-frequency'");

        $this->validator->validate($pages);
    }

    public function testValidateWithAllValidChangeFrequencies(): void
    {
        $validFrequencies = [
            'always',
            'hourly',
            'daily',
            'weekly',
            'monthly',
            'yearly',
            'never',
        ];

        foreach ($validFrequencies as $frequency) {
            $pages = [
                [
                    'loc' => 'https://example.com',
                    'lastmod' => '2024-03-20',
                    'priority' => '0.8',
                    'changefreq' => $frequency,
                ],
            ];

            $this->validator->validate($pages);
            $this->assertTrue(true);
        }
    }

    public function testValidateWithAllValidPriorities(): void
    {
        $validPriorities = [
            '0.0',
            '0.1',
            '0.2',
            '0.3',
            '0.4',
            '0.5',
            '0.6',
            '0.7',
            '0.8',
            '0.9',
            '1.0',
        ];

        foreach ($validPriorities as $priority) {
            $pages = [
                [
                    'loc' => 'https://example.com',
                    'lastmod' => '2024-03-20',
                    'priority' => $priority,
                    'changefreq' => 'daily',
                ],
            ];

            $this->validator->validate($pages);
            $this->assertTrue(true);
        }
    }
}
