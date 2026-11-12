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
    Headers, Status
};

/**
 * ### Represents an immutable HTTP response
 *
 * Encapsulates the HTTP response status and optional message body.
 *
 * Response instances are immutable and independent of the underlying HTTP server or transport implementation.
 *
 * The response body may contain materialized content or provide sequential access to streaming content.
 *
 * The response object is immutable, but reading a stream-backed body may advance the underlying stream position.
 * @since 1.0.0
 */
final readonly class Response {

    /**
     * ### Response headers
     * @since 1.0.0
     */
    private Headers $headers;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Headers::empty() To create an empty HTTP response headers collection.
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status <p>
     * HTTP response status.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Body $body [optional] <p>
     * HTTP response body.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * HTTP response headers.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Status $status,
        private ?Body $body = null,
        ?Headers $headers = null
    ) {

        $this->headers = $headers ?? Headers::empty();

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
     * ### Gets the HTTP response body
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Body HTTP response body.
     */
    public function body ():?Body {

        return $this->body;

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

}