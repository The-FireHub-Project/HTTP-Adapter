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

namespace FireHub\Adapter\HTTP\Body;

use FireHub\Adapter\HTTP\Body;

/**
 * ### Represents a materializable HTTP message body
 *
 * Defines the ability to retrieve the complete HTTP body as a string.
 *
 * The contents method must return the entire body, independently of any previous reading operations.
 *
 * Repeated calls must return identical bytes while the underlying content remains unchanged.
 *
 * Materialization must not change an observable stream position.
 * @since 1.0.0
 */
interface Materializable extends Body {

    /**
     * ### Gets the complete body content
     *
     * Returns all bytes belonging to the HTTP message body.
     *
     * Implementations must not return partial content.
     *
     * Implementations must throw an exception if complete materialization cannot be guaranteed.
     * @since 1.0.0
     *
     * @return string Complete HTTP body content.
     */
    public function contents ():string;

}