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

use FireHub\Adapter\HTTP\Response\ {
    Status\Standard, Headers, Status
};
use FireHub\Adapter\HTTP\Message\Version;
use FireHub\Adapter\HTTP\Body\Str;

/**
 * ### Represents an immutable HTTP response
 *
 * Encapsulates the HTTP response status, protocol version, headers, and response content.
 *
 * Response instances are immutable and independent of the underlying HTTP server implementation.
 *
 * Every modification produces a new response instance.
 * @since 1.0.0
 */
final readonly class Response {

    /**
     * ### Response headers
     * @since 1.0.0
     */
    private Headers $headers;

    /**
     * ### Response body
     * @since 1.0.0
     */
    private Body $body;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Headers::empty() To initialize the response headers.
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status [optional] <p>
     * HTTP response status.
     * </p>
     * @param \FireHub\Adapter\HTTP\Message\Version $version [optional] <p>
     * HTTP protocol version.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * HTTP response headers.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Body $body [optional] <p>
     * HTTP response content.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Status $status = Standard::OK,
        private Version $version = Version::HTTP_1_1,
        ?Headers $headers = null,
        ?Body $body = null
    ) {

        $this->headers = $headers ?? Headers::empty();

        $this->body = $body ?? new Str('');

    }

    /**
     * ### Returns a response with a different HTTP status
     *
     * Preserves the existing headers, body, and protocol version.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status <p>
     * New HTTP response status.
     * </p>
     *
     * @return self New response instance.
     */
    public function withStatus (Status $status):self {

        return new self(
            $status,
            $this->version,
            $this->headers,
            $this->body
        );

    }

    /**
     * ### Returns a response with a different HTTP protocol version
     *
     * Preserves the existing status, headers, and body.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Message\Version $version <p>
     * New HTTP protocol version.
     * </p>
     *
     * @return self New response instance.
     */
    public function withVersion (Version $version):self {

        return new self(
            $this->status,
            $version,
            $this->headers,
            $this->body
        );

    }

    /**
     * ### Returns a response with different HTTP headers
     *
     * Preserves the existing status, body, and protocol version.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Response\Headers $headers <p>
     * New HTTP response headers.
     * </p>
     *
     * @return self New response instance.
     */
    public function withHeaders (Headers $headers):self {

        return new self(
            $this->status,
            $this->version,
            $headers,
            $this->body
        );

    }

    /**
     * ### Returns a response with different content
     *
     * Preserves the existing status, headers, and protocol version.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Body $body <p>
     * New HTTP response content.
     * </p>
     *
     * @return self New response instance.
     */
    public function withBody (Body $body):self {

        return new self(
            $this->status,
            $this->version,
            $this->headers,
            $body
        );

    }

    /**
     * ### Gets the HTTP response status
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Response\Status HTTP response status.
     */
    public function status ():Status {

        return $this->status;

    }

    /**
     * ### Gets the HTTP protocol version
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Message\Version HTTP protocol version.
     */
    public function version ():Version {

        return $this->version;

    }

    /**
     * ### Gets the HTTP response headers
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Response\Headers HTTP response headers.
     */
    public function headers ():Headers {

        return $this->headers;

    }

    /**
     * ### Gets the HTTP response body
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Body HTTP response content.
     */
    public function body ():Body {

        return $this->body;

    }

}