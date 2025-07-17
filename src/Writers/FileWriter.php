<?php

namespace Ryadovoyy\SitemapGenerator\Writers;

use Ryadovoyy\SitemapGenerator\Exceptions\FileAccessException;

/**
 * Default file writer.
 */
class FileWriter implements FileWriterInterface
{
    public function write(string $filePath, string $content): void
    {
        $directory = dirname($filePath);

        if (
            !is_dir($directory)
            && !mkdir($directory, 0755, true)
            && !is_dir($directory)
        ) {
            throw new FileAccessException(
                sprintf("Directory '%s' could not be created", $directory)
            );
        }

        $result = file_put_contents($filePath, $content);

        if ($result === false) {
            throw new FileAccessException(
                sprintf("Could not write to file '%s'", $filePath)
            );
        }
    }
}
