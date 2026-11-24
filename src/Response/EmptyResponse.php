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

namespace FireHub\Adapter\HTTP\Response;

use FireHub\Adapter\HTTP\Response;
use FireHub\Adapter\HTTP\Response\Status\Standard;

/**
 * ### Represents an HTTP response without a message body
 *
 * Constructs an HTTP response without content and defaults to HTTP 204 No Content.
 *
 * Does not automatically assign representation-specific HTTP headers.
 *
 * <code>
 * $response = new EmptyResponse();
 * </code>
 *
 * @since 1.0.0
 */
final readonly class EmptyResponse extends Response {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response::__construct() To initialize the HTTP response.
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status [optional] <p>
     * HTTP response status.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * HTTP response headers.
     * </p>
     *
     * @return void
     */
    public function __construct (
        Status $status = Standard::NO_CONTENT,
        ?Headers $headers = null
    ) {

        parent::__construct(
            $status,
            null,
            $headers
        );

    }

}