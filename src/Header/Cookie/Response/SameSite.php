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

namespace FireHub\Adapter\HTTP\Header\Cookie\Response;

/**
 * ### HTTP SameSite Cookie Attribute
 *
 * Defines the SameSite attribute for HTTP response cookies.
 *
 * SameSite controls whether a browser includes a cookie in requests initiated from a different site.
 *
 * - STRICT: Restricts cookies to same-site requests.
 * - LAX: Allows same-site requests and certain cross-site navigations.
 * - NONE: Allows cross-site requests, subject to browser restrictions.
 *
 * Cookies using SameSite=None must also specify the Secure attribute. This requirement is enforced by the response
 * cookie implementation.
 *
 * <code>
 * $sameSite = SameSite::LAX;
 *
 * $sameSite->value;
 * // Lax
 *
 * $sameSite->headerValue();
 * // SameSite=Lax
 * </code>
 *
 * @since 1.0.0
 */
enum SameSite:string {

    /**
     * ### Strict SameSite Policy
     *
     * Restricts cookie transmission to same-site requests.
     * @since 1.0.0
     */
    case STRICT = 'Strict';

    /**
     * ### Lax SameSite Policy
     *
     * Allows same-site requests and certain cross-site top-level navigations.
     * @since 1.0.0
     */
    case LAX = 'Lax';

    /**
     * ### None SameSite Policy
     *
     * Allows cookies in cross-site contexts.
     *
     * Requires the Secure attribute.
     * @since 1.0.0
     */
    case NONE = 'None';

    /**
     * ### Gets the SameSite Header Attribute
     *
     * Returns the serialized SameSite attribute for use in an HTTP Set-Cookie header field.
     *
     * <code>
     * SameSite::STRICT->headerValue();
     * // SameSite=Strict
     *
     * SameSite::LAX->headerValue();
     * // SameSite=Lax
     *
     * SameSite::NONE->headerValue();
     * // SameSite=None
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized SameSite attribute.
     */
    public function headerValue ():string {

        return 'SameSite='.$this->value;

    }

}