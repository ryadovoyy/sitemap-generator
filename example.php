<?php

require_once __DIR__ . '/vendor/autoload.php';

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
