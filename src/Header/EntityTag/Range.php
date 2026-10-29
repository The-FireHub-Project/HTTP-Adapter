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

namespace FireHub\Adapter\HTTP\Header\EntityTag;

/**
 * ### HTTP entity tag range
 *
 * Defines the wildcard used in conditional requests.
 * @since 1.0.0
 */
enum Range: string {

    /**
     * ### Matches any current representation
     *
     * Matches the existence of a current representation of the target resource.
     * @since 1.0.0
     */
    case ANY = '*';

}