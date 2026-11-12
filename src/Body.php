<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP;

/**
 * ### Represents an HTTP message body
 *
 * Defines the common contract for HTTP message bodies regardless of their underlying storage or reading capabilities.
 *
 * Implementations may represent in-memory content, file-backed streams, or dynamically generated data.
 * @since 1.0.0
 */
interface Body {

    /**
     * ### Gets the complete body size
     *
     * Returns the total number of bytes in the body.
     *
     * The result must not depend on the current reading position.
     *
     * A null value indicates that the complete size is unknown.
     * @since 1.0.0
     *
     * @return null|non-negative-int Total body size in bytes.
     */
    public function size ():?int;

}