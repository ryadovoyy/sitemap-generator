<?php

namespace Ryadovoyy\SitemapGenerator\Validators;

use Ryadovoyy\SitemapGenerator\Exceptions\InvalidDataException;

/**
 * Handles sitemap page data validation.
 */
interface PageValidatorInterface
{
    /**
     * Validates the pages array structure and content.
     *
     * @throws InvalidDataException
     */
    public function validate(array $pages): void;
}
