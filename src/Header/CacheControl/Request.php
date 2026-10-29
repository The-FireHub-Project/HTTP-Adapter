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

namespace FireHub\Adapter\HTTP\Header\CacheControl;

/**
 * ### HTTP request Cache-Control directives
 *
 * Defines cache directives that clients can send in HTTP requests.
 * @since 1.0.0
 */
enum Request:string {

    /**
     * ### Limits the acceptable response age
     *
     * Requires a non-negative number of seconds.
     * @since 1.0.0
     */
    case MAX_AGE = 'max-age';

    /**
     * ### Allows stale cached responses
     *
     * Optionally specifies the maximum acceptable staleness in seconds.
     * @since 1.0.0
     */
    case MAX_STALE = 'max-stale';

    /**
     * ### Requires minimum remaining freshness
     *
     * Requires a non-negative number of seconds.
     * @since 1.0.0
     */
    case MIN_FRESH = 'min-fresh';

    /**
     * ### Requires validation before reuse
     *
     * Prevents reuse of a stored response without successful validation.
     * @since 1.0.0
     */
    case NO_CACHE = 'no-cache';

    /**
     * ### Prevents caching
     *
     * Instructs caches not to store the request or its associated response.
     * @since 1.0.0
     */
    case NO_STORE = 'no-store';

    /**
     * ### Prevents content transformations
     *
     * Instructs intermediaries not to transform the representation content.
     * @since 1.0.0
     */
    case NO_TRANSFORM = 'no-transform';

    /**
     * ### Restricts retrieval to cached responses
     *
     * Requests a stored response without contacting the origin server.
     * @since 1.0.0
     */
    case ONLY_IF_CACHED = 'only-if-cached';

}