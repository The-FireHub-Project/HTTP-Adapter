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

use FireHub\Adapter\HTTP\Response\Status;

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
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status <p>
     * HTTP response status.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Body $body [optional] <p>
     * HTTP response body.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Status $status,
        private ?Body $body = null
    ) {}

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

}