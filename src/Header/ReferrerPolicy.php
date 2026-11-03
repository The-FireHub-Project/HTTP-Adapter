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

namespace FireHub\Adapter\HTTP\Header;

/**
 * ### Referrer Policy
 *
 * Defines how much referrer information a browser may include in requests originating from the protected document.
 *
 * The policy controls the Referer HTTP request header and applies to supported navigation and resource requests.
 * @since 1.0.0
 */
enum ReferrerPolicy:string {

    /**
     * ### No Referrer
     *
     * Prevents the browser from sending the Referer header with any request.
     * @since 1.0.0
     */
    case NO_REFERRER = 'no-referrer';

    /**
     * ### No Referrer When Downgrade
     *
     * Sends the full referrer URL for same-origin and cross-origin requests, but omits it when navigating from HTTPS
     * to HTTP.
     * @since 1.0.0
     */
    case NO_REFERRER_WHEN_DOWNGRADE = 'no-referrer-when-downgrade';

    /**
     * ### Origin
     *
     * Sends only the origin of the referring document, excluding the path, query, and fragment.
     *
     * The origin is sent even when navigating from HTTPS to HTTP.
     * @since 1.0.0
     */
    case ORIGIN = 'origin';

    /**
     * ### Origin When Cross Origin
     *
     * Sends the full referrer URL for same-origin requests and only the origin for cross-origin requests, including
     * HTTPS-to-HTTP requests.
     * @since 1.0.0
     */
    case ORIGIN_WHEN_CROSS_ORIGIN = 'origin-when-cross-origin';

    /**
     * ### Same Origin
     *
     * Sends the full referrer URL for same-origin requests.
     *
     * Omits the Referer header entirely for cross-origin requests.
     * @since 1.0.0
     */
    case SAME_ORIGIN = 'same-origin';

    /**
     * ### Strict Origin
     *
     * Sends only the origin when the destination is at least as secure as the referring document.
     *
     * Omits the Referer header when navigating from HTTPS to HTTP.
     * @since 1.0.0
     */
    case STRICT_ORIGIN = 'strict-origin';

    /**
     * ### Strict Origin When Cross Origin
     *
     * Sends the full referrer URL for same-origin requests and only the origin for cross-origin requests.
     *
     * Omits the Referer header when navigating from HTTPS to HTTP.
     * @since 1.0.0
     */
    case STRICT_ORIGIN_WHEN_CROSS_ORIGIN = 'strict-origin-when-cross-origin';

    /**
     * ### Unsafe URL
     *
     * Sends the full referrer URL for both same-origin and cross-origin requests, including HTTPS-to-HTTP requests.
     *
     * This policy may expose sensitive URL information and should generally be avoided.
     * @since 1.0.0
     */
    case UNSAFE_URL = 'unsafe-url';

}