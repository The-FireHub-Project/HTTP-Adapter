<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Language;

/**
 * ### Defines HTTP language ranges
 *
 * Represents the wildcard language range used in Accept-Language negotiation.
 * @since 1.0.0
 */
enum Range:string {

    /**
     * ### Any language
     *
     * Matches any language not otherwise excluded by content negotiation.
     * @since 1.0.0
     */
    case ANY = '*';

}