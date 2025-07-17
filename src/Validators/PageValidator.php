<?php

namespace Ryadovoyy\SitemapGenerator\Validators;

use Ryadovoyy\SitemapGenerator\Enums\ChangeFrequency;
use Ryadovoyy\SitemapGenerator\Exceptions\InvalidDataException;

/**
 * Default page validator.
 */
class PageValidator implements PageValidatorInterface
{
    private const VALID_PRIORITIES = [
        '0.0',
        '0.1',
        '0.2',
        '0.3',
        '0.4',
        '0.5',
        '0.6',
        '0.7',
        '0.8',
        '0.9',
        '1.0',
    ];

    private const UNESCAPED_ENTITIES = [
        '&' => '&amp;',
        "'" => '&apos;',
        '"' => '&quot;',
        '>' => '&gt;',
        '<' => '&lt;'
    ];

    public function validate(array $pages): void
    {
        if (empty($pages)) {
            throw new InvalidDataException('Pages array cannot be empty');
        }

        if (count($pages) > 50000) {
            throw new InvalidDataException('Pages array cannot contain more than 50,000 elements');
        }

        foreach ($pages as $page) {
            $this->validatePageStructure($page);
            $this->validatePageContent($page);
        }
    }

    /**
     * @throws InvalidDataException
     */
    private function validatePageStructure(array $page): void
    {
        if (!isset($page['loc'], $page['lastmod'], $page['priority'], $page['changefreq'])) {
            throw new InvalidDataException(
                'Each page must contain loc, lastmod, priority and changefreq keys'
            );
        }
    }

    /**
     * @throws InvalidDataException
     */
    private function validatePageContent(array $page): void
    {
        $this->validateUrl($page['loc']);
        $this->validateLastModificationDate($page['lastmod']);
        $this->validatePriority($page['priority']);
        $this->validateChangeFrequency($page['changefreq']);
    }

    /**
     * @throws InvalidDataException
     */
    private function validateUrl(mixed $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidDataException(
                sprintf("Invalid URL: '%s'", $url)
            );
        }

        if (strlen($url) > 2048) {
            throw new InvalidDataException('URL must be at most 2,048 characters');
        }

        foreach (self::UNESCAPED_ENTITIES as $char => $entity) {
            if ($char === '&') {
                $ampCount = substr_count($url, $char);
                $ampEntityCount = substr_count($url, $entity);
                $isUnescaped = $ampCount > $ampEntityCount;
            } else {
                $isUnescaped = str_contains($url, $char);
            }

            if ($isUnescaped) {
                throw new InvalidDataException(
                    sprintf(
                        "URL contains unescaped '%s' character. It must be escaped as '%s'",
                        $char,
                        $entity
                    )
                );
            }
        }
    }

    /**
     * @throws InvalidDataException
     */
    private function validateLastModificationDate(mixed $date): void
    {
        if (!$this->isValidStrictYmdDate($date)) {
            throw new InvalidDataException(
                sprintf(
                    "Last modification date must be a valid date string"
                    . " in the W3C Datetime format (YYYY-MM-DD), got: '%s'",
                    $date
                )
            );
        }
    }

    private function isValidStrictYmdDate(mixed $date): bool
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches) !== 1) {
            return false;
        }

        if (!checkdate($matches[2], $matches[3], $matches[1])) {
            return false;
        }

        $dt = \DateTime::createFromFormat('Y-m-d', $date);
        return $dt && $dt->format('Y-m-d') === $date;
    }

    /**
     * @throws InvalidDataException
     */
    private function validatePriority(mixed $priority): void
    {
        if (
            !is_string($priority)
            || !in_array($priority, self::VALID_PRIORITIES, true)
        ) {
            throw new InvalidDataException(
                sprintf(
                    "Priority must be a string between 0.0 and 1.0, got: '%s'",
                    $priority
                )
            );
        }
    }

    /**
     * @throws InvalidDataException
     */
    private function validateChangeFrequency(mixed $changeFrequency): void
    {
        if (!ChangeFrequency::tryFrom($changeFrequency)) {
            throw new InvalidDataException(
                sprintf("Invalid change frequency: '%s'", $changeFrequency)
            );
        }
    }
}
