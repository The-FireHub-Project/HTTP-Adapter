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

namespace FireHub\Adapter\HTTP\Header\Range;

/**
 * ### Defines HTTP range units
 * @since 1.0.0
 */
enum Unit:string {

    /**
     * ### Byte range unit
     * @since 1.0.0
     */
    case BYTES = 'bytes';

}