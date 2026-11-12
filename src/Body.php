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
     * ### Gets the HTTP body size
     *
     * Returns the number of bytes currently available for transmission.
     *
     * For materialized bodies, the size represents the complete content and remains constant.
     *
     * For stream-backed bodies, the size represents the remaining readable bytes from the current stream position
     * and may decrease as the stream is consumed.
     *
     * Returns null when the available size cannot be determined.
     *
     * Calling this method must not consume body content or modify the underlying stream position.
     * @since 1.0.0
     *
     * @return null|non-negative-int Total body size in bytes.
     */
    public function size ():?int;

}