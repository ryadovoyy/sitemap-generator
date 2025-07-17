<?php

namespace Ryadovoyy\SitemapGenerator;

/**
 * Represents a single item in the sitemap.
 */
class SitemapItem
{
    /**
     * @param string $loc The URL of the page
     * @param ?string $lastmod Last modification date
     * @param ?string $priority Priority of the page
     * @param ?string $changefreq Change frequency
     */
    public function __construct(
        private readonly string $loc,
        private readonly ?string $lastmod,
        private readonly ?string $priority,
        private readonly ?string $changefreq
    ) {}

    public function getLoc(): string
    {
        return $this->loc;
    }

    public function getLastmod(): ?string
    {
        return $this->lastmod;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function getChangefreq(): ?string
    {
        return $this->changefreq;
    }

    public function toArray(): array
    {
        return [
            'loc' => $this->loc,
            'lastmod' => $this->lastmod,
            'priority' => $this->priority,
            'changefreq' => $this->changefreq,
        ];
    }
}
