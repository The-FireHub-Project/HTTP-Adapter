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
 * Defines the contract for accessing the raw content of an HTTP message.
 *
 * Implementations may store the body in memory or provide access to an underlying stream.
 *
 * The body does not interpret its content as JSON, XML, or form data.
 * @since 1.0.0
 */
interface Body {

    /**
     * ### Returns the complete body content
     *
     * Implementations must return the complete body content.
     *
     * Reading a body may involve I/O operations and memory allocation.
     * @since 1.0.0
     *
     * @return string Raw body content.
     */
    public function contents ():string;

    /**
     * ### Returns the body size
     *
     * Returns the size in bytes when known.
     *
     * A null value indicates that the size cannot be determined
     * without consuming the body.
     * @since 1.0.0
     *
     * @return null|non-negative-int Body size in bytes.
     */
    public function size ():?int;

}