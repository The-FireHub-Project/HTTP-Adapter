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

use FireHub\Adapter\HTTP\Message\Version;
use FireHub\Adapter\HTTP\Request\ {
    Headers, Method, Target
};

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
     * ### Request headers
     * @since 1.0.0
     */
    private Headers $headers;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Headers::empty() To create an empty HTTP request headers collection.
     *
     * @param \FireHub\Adapter\HTTP\Message\Version $version <p>
     * HTTP request version.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Target $target <p>
     * HTTP request target.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Body $body [optional] <p>
     * HTTP request body.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Request\Headers $headers [optional] <p>
     * HTTP request headers.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Version $version,
        private Method $method,
        private Target $target,
        private ?Body $body = null,
        ?Headers $headers = null
    ) {

        $this->headers = $headers ?? Headers::empty();

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
     * @return \FireHub\Adapter\HTTP\Request\Target HTTP request target.
     */
    public function target ():Target {

        return $this->target;

    }

    /**
     * ### Gets the HTTP request body
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Body HTTP request body.
     */
    public function body ():?Body {

        return $this->body;

    }

    /**
     * ### Gets the HTTP request headers
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request\Headers HTTP request headers.
     */
    public function headers ():Headers {

        return $this->headers;

    }

}