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

namespace FireHub\Adapter\HTTP\Cookie;

/**
 * Defines the SameSite attribute of an HTTP cookie.
 *
 * Controls whether a browser includes a cookie in cross-site requests,  providing protection against certain
 * cross-site request forgery attacks.
 * @since 1.0.0
 */
enum SameSite:string {

    /**
     * ### Cookie is sent only in same-site contexts
     * @since 1.0.0
     */
    case STRICT = 'Strict';

    /**
     * ### Cookie is sent in same-site contexts and certain cross-site top-level navigations
     * @since 1.0.0
     */
    case LAX = 'Lax';

    /**
     * ### Cookie may be sent in both same-site and cross-site contexts
     *
     * Requires the Secure attribute.
     * @since 1.0.0
     */
    case NONE = 'None';

}