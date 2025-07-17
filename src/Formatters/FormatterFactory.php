<?php

namespace Ryadovoyy\SitemapGenerator\Formatters;

use Ryadovoyy\SitemapGenerator\Enums\SitemapFormat;
use Ryadovoyy\SitemapGenerator\Exceptions\UnsupportedFormatException;

/**
 * Factory for creating formatter instances.
 */
class FormatterFactory
{
    /**
     * Creates a formatter for the specified format.
     *
     * @throws UnsupportedFormatException
     */
    public function create(SitemapFormat $format): FormatterInterface
    {
        return match ($format) {
            SitemapFormat::Xml => new XmlFormatter(),
            SitemapFormat::Csv => new CsvFormatter(),
            SitemapFormat::Json => new JsonFormatter(),
            default => throw new UnsupportedFormatException("Unsupported format: {$format->value}"),
        };
    }
}
