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

use FireHub\Adapter\HTTP\Body;
use FireHub\Adapter\HTTP\Request\ {
    Headers, Method, Target
};
use FireHub\Adapter\HTTP\Message\Version;
use FireHub\Adapter\HTTP\Body\Str;

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
     * ### Request body
     * @since 1.0.0
     */
    private Body $body;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Headers::empty() To create an empty HTTP request headers collection.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Target $target <p>
     * HTTP request target.
     * </p>
     * @param \FireHub\Adapter\HTTP\Message\Version $version [optional] <p>
     * HTTP request version.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Headers|null $headers [optional] <p>
     * HTTP request headers.
     * </p>
     * @param \FireHub\Adapter\HTTP\Body|null $body [optional] <p>
     * HTTP request body.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Method $method,
        private Target $target,
        private Version $version = Version::HTTP_1_1,
        ?Headers $headers = null,
        ?Body $body = null
    ) {

        $this->headers = $headers ?? Headers::empty();

        $this->body = $body ?? new Str('');

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
            $this->target,
            $this->version,
            $this->headers,
            $this->body
        );

    }

    /**
     * ### Returns a request with a different HTTP target
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Target $target <p>
     * New HTTP target.
     * </p>
     *
     * @return self New request instance.
     */
    public function withTarget (Target $target):self {

        return new self(
            $this->method,
            $target,
            $this->version,
            $this->headers,
            $this->body
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
            $this->target,
            $version,
            $this->headers,
            $this->body
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
            $this->target,
            $this->version,
            $headers,
            $this->body
        );

    }

    /**
     * ### Returns a request with a different body
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Body $body <p>
     * New HTTP request body.
     * </p>
     *
     * @return self New request instance.
     */
    public function withBody (Body $body):self {

        return new self(
            $this->method,
            $this->target,
            $this->version,
            $this->headers,
            $body
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
     * ### Gets the HTTP request target
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request\Target HTTP request target.
     */
    public function target ():Target {

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
     * ### Gets the HTTP request body
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Body HTTP request body.
     */
    public function body ():Body {

        return $this->body;

    }

}