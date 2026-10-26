<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Request;

/**
 * ### Defines standard HTTP request methods
 *
 * Provides the standardized HTTP request methods used to identify the intended action performed on a target resource.
 *
 * Implements the Method contract to allow standard and custom HTTP methods to be handled through a common interface.
 * @since 1.0.0
 */
enum Standard:string implements Method {

    /**
     * ### HEAD method
     *
     * Requests the response headers without the response content.
     * @since 1.0.0
     */
    case HEAD = 'HEAD';

    /**
     * ### GET method
     *
     * Requests a representation of the target resource.
     * @since 1.0.0
     */
    case GET = 'GET';

    /**
     * ### POST method
     *
     * Submits data to the target resource for processing.
     * @since 1.0.0
     */
    case POST = 'POST';

    /**
     * ### PUT method
     *
     * Creates or replaces the state of the target resource.
     * @since 1.0.0
     */
    case PUT = 'PUT';

    /**
     * ### DELETE method
     *
     * Requests removal of the target resource.
     * @since 1.0.0
     */
    case DELETE = 'DELETE';

    /**
     * ### CONNECT method
     *
     * Establishes a tunnel to the target server.
     * @since 1.0.0
     */
    case CONNECT = 'CONNECT';

    /**
     * ### OPTIONS method
     *
     * Requests information about communication options for the target resource.
     * @since 1.0.0
     */
    case OPTIONS = 'OPTIONS';

    /**
     * ### TRACE method
     *
     * Performs a diagnostic loopback of the received request.
     * @since 1.0.0
     */
    case TRACE = 'TRACE';

    /**
     * ### PATCH method
     *
     * Applies partial modifications to the target resource.
     * @since 1.0.0
     */
    case PATCH = 'PATCH';

    /**
     * ### Gets the HTTP request method
     *
     * Returns the HTTP method token associated with the enum case.
     * @since 1.0.0
     *
     * @return non-empty-string HTTP request method token.
     */
    public function value ():string {

        return $this->value;

    }

}