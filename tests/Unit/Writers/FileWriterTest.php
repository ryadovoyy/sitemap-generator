<?php

namespace Ryadovoyy\SitemapGenerator\Tests\Unit\Writers;

use PHPUnit\Framework\TestCase;
use Ryadovoyy\SitemapGenerator\Exceptions\FileAccessException;
use Ryadovoyy\SitemapGenerator\Writers\FileWriter;

class FileWriterTest extends TestCase
{
    private FileWriter $writer;
    private string $tempDir;

    protected function setUp(): void
    {
        $this->writer = new FileWriter();
        $this->tempDir = sys_get_temp_dir() . '/sitemap-test-' . uniqid();
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            array_map('unlink', glob($this->tempDir . '/*.*'));
            rmdir($this->tempDir);
        }
    }

    public function testWriteToNewFile(): void
    {
        $filePath = $this->tempDir . '/test.txt';
        $content = 'Test content';

        $this->writer->write($filePath, $content);

        $this->assertFileExists($filePath);
        $this->assertEquals($content, file_get_contents($filePath));
    }

    public function testWriteToExistingFile(): void
    {
        $filePath = $this->tempDir . '/test.txt';
        $initialContent = 'Initial content';
        $newContent = 'New content';

        mkdir($this->tempDir);
        file_put_contents($filePath, $initialContent);

        $this->writer->write($filePath, $newContent);

        $this->assertFileExists($filePath);
        $this->assertEquals($newContent, file_get_contents($filePath));
    }

    public function testWriteToNestedDirectory(): void
    {
        $nestedDir = $this->tempDir . '/nested/dir';
        $filePath = $nestedDir . '/test.txt';
        $content = 'Test content';

        $this->writer->write($filePath, $content);

        $this->assertDirectoryExists($nestedDir);
        $this->assertFileExists($filePath);
        $this->assertEquals($content, file_get_contents($filePath));
    }

    public function testWriteWithDirectoryCreationError(): void
    {
        $filePath = $this->tempDir . '/file.txt';
        mkdir($this->tempDir);
        file_put_contents($filePath, 'Some content');

        $this->expectException(FileAccessException::class);
        $this->expectExceptionMessage("Directory '{$filePath}' could not be created");

        $this->writer->write($filePath . '/test.txt', 'Test content');
    }

    public function testWriteWithFileWriteError(): void
    {
        $filePath = $this->tempDir;
        mkdir($filePath);

        $this->expectException(FileAccessException::class);
        $this->expectExceptionMessage("Could not write to file '{$filePath}'");

        $this->writer->write($filePath, 'Test content');
    }
}
