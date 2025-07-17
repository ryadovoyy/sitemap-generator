<?php

namespace Ryadovoyy\SitemapGenerator\Writers;

use Ryadovoyy\SitemapGenerator\Exceptions\FileAccessException;

/**
 * Handles writing content to persistent storage.
 */
interface FileWriterInterface
{
    /**
     * Writes content to specified file path.
     *
     * @throws FileAccessException
     */
    public function write(string $filePath, string $content): void;
}
