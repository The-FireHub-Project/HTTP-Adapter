<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP;

use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Adapter\HTTP\Message\Version;

/**
 * ### Represents an HTTP request
 *
 * Encapsulates the HTTP request method and request target.
 *
 * Request instances are immutable and independent of the underlying HTTP server implementation.
 * @since 1.0.0
 */
final readonly class Request {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param string $target <p>
     * HTTP request target.
     * </p>
     * @param \FireHub\Adapter\HTTP\Message\Version $version [optional] <p>
     * HTTP request version.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Method $method,
        private string $target,
        private Version $version = Version::HTTP_1_1
    ) {}

    /**
     * ### Gets the HTTP request method
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request\Method HTTP request method.
     */
    public function method ():Method {

        return $this->method;

    }

    /**
     * ### Gets the HTTP request target
     * @since 1.0.0
     *
     * @return string HTTP request target.
     */
    public function target ():string {

        return $this->target;

    }

    /**
     * ### Gets the HTTP request version
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Message\Version HTTP request version.
     */
    public function version ():Version {

        return $this->version;

    }

}