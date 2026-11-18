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
 * ### HTTP response Cache-Control directives
 *
 * Defines cache directives that servers can send in HTTP responses.
 * @since 1.0.0
 */
enum Response:string {

    /**
     * ### Specifies the maximum response age
     *
     * Requires a non-negative number of seconds.
     * @since 1.0.0
     */
    case MAX_AGE = 'max-age';

    /**
     * ### Requires validation before reuse
     *
     * Optionally identifies response header fields requiring validation.
     * @since 1.0.0
     */
    case NO_CACHE = 'no-cache';

    /**
     * ### Prevents response storage
     *
     * Instructs caches not to store the response.
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
     * ### Requires revalidation of stale responses
     *
     * Prevents reuse of a stale response without successful validation.
     * @since 1.0.0
     */
    case MUST_REVALIDATE = 'must-revalidate';

    /**
     * ### Requires shared-cache revalidation
     *
     * Prevents shared caches from reusing stale responses without validation.
     * @since 1.0.0
     */
    case PROXY_REVALIDATE = 'proxy-revalidate';

    /**
     * ### Allows shared-cache storage
     *
     * Explicitly permits storage by shared caches.
     * @since 1.0.0
     */
    case PUBLIC = 'public';

    /**
     * ### Restricts shared-cache storage
     *
     * Optionally identifies response header fields restricted from shared caches.
     * @since 1.0.0
     */
    case PRIVATE = 'private';

    /**
     * ### Specifies the maximum age for shared caches
     *
     * Requires a non-negative number of seconds.
     * @since 1.0.0
     */
    case S_MAXAGE = 's-maxage';

    /**
     * ### Indicates immutable fresh content
     *
     * Indicates that a fresh response is not expected to change.
     * @since 1.0.0
     */
    case IMMUTABLE = 'immutable';

    /**
     * ### Allows stale responses during revalidation
     *
     * Requires a non-negative number of seconds.
     * @since 1.0.0
     */
    case STALE_WHILE_REVALIDATE = 'stale-while-revalidate';

    /**
     * ### Allows stale responses when errors occur
     *
     * Requires a non-negative number of seconds.
     * @since 1.0.0
     */
    case STALE_IF_ERROR = 'stale-if-error';

    /**
     * ### Requires understanding of response semantics
     *
     * Requires caches to understand the response status code requirements.
     * @since 1.0.0
     */
    case MUST_UNDERSTAND = 'must-understand';

}