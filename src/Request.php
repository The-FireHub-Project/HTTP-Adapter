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

use FireHub\Adapter\HTTP\Request\ {
    Method, Headers
};
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
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Headers|null $headers [optional] <p>
     * HTTP request headers.
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
        ?Headers $headers = null,
        private string $target,
        private Version $version = Version::HTTP_1_1
    ) {

        $this->headers = $headers ?? Headers::empty();

    }

    /**
     * ### Returns a request with a different HTTP method
     *
     * Preserves the existing request target, protocol version, and headers.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * New HTTP request method.
     * </p>
     *
     * @return self New request instance.
     */
    public function withMethod (Method $method):self {

        return new self(
            $method,
            $this->headers,
            $this->target,
            $this->version
        );

    }

    /**
     * ### Returns a request with different HTTP headers
     *
     * Preserves the existing method, request target, and protocol version.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Headers $headers <p>
     * New HTTP request headers.
     * </p>
     *
     * @return self New request instance.
     */
    public function withHeaders (Headers $headers):self {

        return new self(
            $this->method,
            $headers,
            $this->target,
            $this->version
        );

    }

    /**
     * ### Returns a request with a different request target
     *
     * Preserves the existing method, protocol version, and headers.
     * @since 1.0.0
     *
     * @param string $target <p>
     * New HTTP request target.
     * </p>
     *
     * @return self New request instance.
     */
    public function withTarget (string $target):self {

        return new self(
            $this->method,
            $this->headers,
            $target,
            $this->version
        );

    }

    /**
     * ### Returns a request with a different HTTP protocol version
     *
     * Preserves the existing method, request target, and headers.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Message\Version $version <p>
     * New HTTP protocol version.
     * </p>
     *
     * @return self New request instance.
     */
    public function withVersion (Version $version):self {

        return new self(
            $this->method,
            $this->headers,
            $this->target,
            $version
        );

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
     * ### Gets the HTTP request headers
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request\Headers HTTP request headers.
     */
    public function headers ():Headers {

        return $this->headers;

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