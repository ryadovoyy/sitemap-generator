<?php

namespace Ryadovoyy\SitemapGenerator\Tests\Unit\Formatters;

use PHPUnit\Framework\TestCase;
use Ryadovoyy\SitemapGenerator\Enums\SitemapFormat;
use Ryadovoyy\SitemapGenerator\Formatters\CsvFormatter;
use Ryadovoyy\SitemapGenerator\Formatters\FormatterFactory;
use Ryadovoyy\SitemapGenerator\Formatters\JsonFormatter;
use Ryadovoyy\SitemapGenerator\Formatters\XmlFormatter;

class FormatterFactoryTest extends TestCase
{
    private FormatterFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FormatterFactory();
    }

    public function testCreateXmlFormatter(): void
    {
        $formatter = $this->factory->create(SitemapFormat::Xml);
        $this->assertInstanceOf(XmlFormatter::class, $formatter);
    }

    public function testCreateCsvFormatter(): void
    {
        $formatter = $this->factory->create(SitemapFormat::Csv);
        $this->assertInstanceOf(CsvFormatter::class, $formatter);
    }

    public function testCreateJsonFormatter(): void
    {
        $formatter = $this->factory->create(SitemapFormat::Json);
        $this->assertInstanceOf(JsonFormatter::class, $formatter);
    }
}
