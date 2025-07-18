<?php

namespace Ryadovoyy\SitemapGenerator;

use Ryadovoyy\SitemapGenerator\Enums\SitemapFormat;
use Ryadovoyy\SitemapGenerator\Exceptions\FileAccessException;
use Ryadovoyy\SitemapGenerator\Exceptions\InvalidDataException;
use Ryadovoyy\SitemapGenerator\Exceptions\UnsupportedFormatException;
use Ryadovoyy\SitemapGenerator\Formatters\FormatterFactory;
use Ryadovoyy\SitemapGenerator\Formatters\FormatterInterface;
use Ryadovoyy\SitemapGenerator\Validators\PageValidator;
use Ryadovoyy\SitemapGenerator\Validators\PageValidatorInterface;
use Ryadovoyy\SitemapGenerator\Writers\FileWriter;
use Ryadovoyy\SitemapGenerator\Writers\FileWriterInterface;

/**
 * Main sitemap generator class that handles creation of sitemap files in various formats.
 */
class SitemapGenerator
{
    private string $filePath;
    private array $pages;

    private PageValidatorInterface $pageValidator;
    private FormatterInterface $formatter;
    private FileWriterInterface $fileWriter;

    /**
     * @param array $pages Array of pages with keys: loc, lastmod, priority, changefreq
     * @param SitemapFormat $format Output format
     * @param string $filePath Path where to save the generated sitemap
     * @throws UnsupportedFormatException
     */
    public function __construct(array $pages, SitemapFormat $format, string $filePath)
    {
        $this->pages = $pages;
        $this->filePath = $filePath;

        $this->setPageValidator(new PageValidator());
        $this->formatter = (new FormatterFactory())->create($format);
        $this->setFileWriter(new FileWriter());
    }

    public function setPageValidator(PageValidatorInterface $pageValidator): void
    {
        $this->pageValidator = $pageValidator;
    }

    public function setFileWriter(FileWriterInterface $fileWriter): void
    {
        $this->fileWriter = $fileWriter;
    }

    /**
     * Generates the sitemap file and saves it to the predefined path.
     *
     * @throws InvalidDataException
     * @throws FileAccessException
     */
    public function generate(): void
    {
        $this->pageValidator->validate($this->pages);
        $items = $this->createItems($this->pages);

        $content = $this->formatter->format($items);
        $this->fileWriter->write($this->filePath, $content);
    }

    /**
     * @return SitemapItem[] Array of sitemap items
     */
    private function createItems(array $pages): array
    {
        return array_map(function ($page) {
            return new SitemapItem(
                $page['loc'],
                $page['lastmod'],
                $page['priority'],
                $page['changefreq']
            );
        }, $pages);
    }
}
