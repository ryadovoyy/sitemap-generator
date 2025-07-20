<?php

namespace Ryadovoyy\SitemapGenerator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ryadovoyy\SitemapGenerator\Enums\SitemapFormat;
use Ryadovoyy\SitemapGenerator\Exceptions\FileAccessException;
use Ryadovoyy\SitemapGenerator\Exceptions\InvalidDataException;
use Ryadovoyy\SitemapGenerator\Formatters\FormatterInterface;
use Ryadovoyy\SitemapGenerator\SitemapGenerator;
use Ryadovoyy\SitemapGenerator\SitemapItem;
use Ryadovoyy\SitemapGenerator\Validators\PageValidatorInterface;
use Ryadovoyy\SitemapGenerator\Writers\FileWriterInterface;

class SitemapGeneratorTest extends TestCase
{
    private SitemapGenerator $generator;
    private MockObject&PageValidatorInterface $pageValidator;
    private MockObject&FormatterInterface $formatter;
    private MockObject&FileWriterInterface $fileWriter;

    private array $validPages;
    private string $filePath;

    protected function setUp(): void
    {
        $this->validPages = [
            [
                'loc' => 'https://example.com',
                'lastmod' => '2024-03-20',
                'priority' => '0.8',
                'changefreq' => 'daily',
            ],
        ];
        $this->filePath = '/tmp/sitemap.xml';

        $this->pageValidator = $this->createMock(PageValidatorInterface::class);
        $this->formatter = $this->createMock(FormatterInterface::class);
        $this->fileWriter = $this->createMock(FileWriterInterface::class);

        $this->generator = new SitemapGenerator($this->validPages, SitemapFormat::Xml, $this->filePath);
        $this->generator->setPageValidator($this->pageValidator);
        $this->generator->setFileWriter($this->fileWriter);

        $reflection = new \ReflectionClass($this->generator);
        $formatterProperty = $reflection->getProperty('formatter');
        $formatterProperty->setValue($this->generator, $this->formatter);
    }

    public function testGenerateWithValidData(): void
    {
        $this->pageValidator->expects($this->once())
            ->method('validate')
            ->with($this->validPages);

        $expectedContent = '<?xml version="1.0"?><urlset></urlset>';
        $this->formatter->expects($this->once())
            ->method('format')
            ->willReturn($expectedContent);

        $this->fileWriter->expects($this->once())
            ->method('write')
            ->with($this->filePath, $expectedContent);

        $this->generator->generate();
    }

    public function testGenerateWithInvalidData(): void
    {
        $this->pageValidator->expects($this->once())
            ->method('validate')
            ->with($this->validPages)
            ->willThrowException(new InvalidDataException('Invalid data'));

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Invalid data');

        $this->generator->generate();
    }

    public function testGenerateWithFileWriteError(): void
    {
        $this->pageValidator->expects($this->once())
            ->method('validate')
            ->with($this->validPages);

        $content = '<?xml version="1.0"?><urlset></urlset>';
        $this->formatter->expects($this->once())
            ->method('format')
            ->willReturn($content);

        $this->fileWriter->expects($this->once())
            ->method('write')
            ->with($this->filePath, $content)
            ->willThrowException(new FileAccessException('Cannot write file'));

        $this->expectException(FileAccessException::class);
        $this->expectExceptionMessage('Cannot write file');

        $this->generator->generate();
    }

    public function testCreateItemsCreatesCorrectSitemapItems(): void
    {
        $this->pageValidator->expects($this->once())
            ->method('validate')
            ->with($this->validPages);

        $this->formatter->expects($this->once())
            ->method('format')
            ->with($this->callback(function ($items) {
                $this->assertCount(1, $items);
                $this->assertInstanceOf(SitemapItem::class, $items[0]);

                /** @var SitemapItem $item */
                $item = $items[0];
                $this->assertEquals('https://example.com', $item->getLoc());
                $this->assertEquals('2024-03-20', $item->getLastmod());
                $this->assertEquals('0.8', $item->getPriority());
                $this->assertEquals('daily', $item->getChangefreq());

                return true;
            }))
            ->willReturn('');

        $this->generator->generate();
    }
}
