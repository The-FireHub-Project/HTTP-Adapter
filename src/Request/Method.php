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

namespace FireHub\Adapter\HTTP\Request;

/**
 * ### Defines an HTTP request method
 *
 * Provides a common contract for standard and custom HTTP request methods.
 *
 * Implementations expose the HTTP method token used when processing or transmitting HTTP requests.
 * @since 1.0.0
 */
interface Method {

    /**
     * ### Gets the HTTP request method
     *
     * Returns the HTTP method token represented by this instance.
     * @since 1.0.0
     *
     * @return non-empty-string HTTP request method token.
     */
    public function value ():string;

}