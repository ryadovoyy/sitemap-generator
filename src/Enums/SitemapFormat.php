<?php

namespace Ryadovoyy\SitemapGenerator\Enums;

/**
 * Supported sitemap formats.
 */
enum SitemapFormat: string
{
    case Xml = 'xml';
    case Csv = 'csv';
    case Json = 'json';
}
