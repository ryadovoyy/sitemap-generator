# sitemap-generator

A PHP library for generating sitemaps in XML, CSV and JSON formats.

## Install

```bash
composer require ryadovoyy/sitemap-generator
```

## Usage example

```php
use Ryadovoyy\SitemapGenerator\Enums\SitemapFormat;
use Ryadovoyy\SitemapGenerator\Exceptions\FileAccessException;
use Ryadovoyy\SitemapGenerator\Exceptions\InvalidDataException;
use Ryadovoyy\SitemapGenerator\Exceptions\UnsupportedFormatException;
use Ryadovoyy\SitemapGenerator\SitemapGenerator;

$pages = [
    [
        'loc' => 'https://site.ru/',
        'lastmod' => '2020-12-14',
        'priority' => '1.0',
        'changefreq' => 'hourly',
    ],
    [
        'loc' => 'https://site.ru/news',
        'lastmod' => '2020-12-10',
        'priority' => '0.5',
        'changefreq' => 'daily',
    ],
];

try {
    $generator = new SitemapGenerator($pages, SitemapFormat::Xml, __DIR__ . '/sitemap.xml');
    $generator->generate();
    echo "Sitemap generated successfully!\n";
} catch (UnsupportedFormatException | InvalidDataException | FileAccessException $e) { // or just catch SitemapException
    echo $e->getMessage() . "\n";
}
```

## Page validation

The sitemap page validation is primarily done according to the [XML schema](https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd), so the library provides only basic checks. The library doesn't transform any values from the input page array. Ensuring full compliance with the [protocol](https://www.sitemaps.org/protocol.html) (same host for all URLs, etc.) is the responsibility of a user.

Here are some features of the default validation:

- Not only `loc`, but also `lastmod`, `changefreq` and `priority` properties are all required in each page entry
- Only `YYYY-MM-DD` date format for `lastmod` is allowed

You can change the default validation by writing a custom validator that implements the `PageValidatorInterface` and setting it after the initialization:

```php
$generator = new SitemapGenerator($pages, SitemapFormat::Xml, __DIR__ . '/sitemap.xml');
$generator->setPageValidator($customValidator);
$generator->generate();
```
