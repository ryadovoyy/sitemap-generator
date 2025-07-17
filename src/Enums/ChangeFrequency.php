<?php

namespace Ryadovoyy\SitemapGenerator\Enums;

/**
 * Allowed values for the `changefreq` sitemap field.
 */
enum ChangeFrequency: string
{
    case Always = 'always';
    case Hourly = 'hourly';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
    case Never = 'never';
}
