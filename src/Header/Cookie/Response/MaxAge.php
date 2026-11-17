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

namespace FireHub\Adapter\HTTP\Header\Cookie\Response;

use FireHub\Core\Type\ValueObject;

/**
 * ### HTTP Cookie Max-Age Attribute
 *
 * Represents the Max-Age attribute of an HTTP Set-Cookie header.
 *
 * Max-Age specifies the lifetime of a cookie in seconds.
 *
 * A positive value specifies the lifetime, while zero or a negative value instructs the user agent to expire the
 * cookie.
 *
 * <code>
 * $maxAge = new MaxAge(3600);
 *
 * $maxAge->value();
 * // 3600
 *
 * $maxAge->headerValue();
 * // Max-Age=3600
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<int>
 */
final readonly class MaxAge extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param int $seconds <p>
     * Cookie lifetime in seconds.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private int $seconds
    ) {}

    /**
     * ### Checks Whether the Cookie Expires Immediately
     *
     * A Max-Age value of zero or less indicates that the cookie should expire immediately.
     *
     * <code>
     * (new MaxAge(0))->isExpired();
     * // true
     *
     * (new MaxAge(-1))->isExpired();
     * // true
     *
     * (new MaxAge(3600))->isExpired();
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the cookie should expire immediately.
     */
    public function isExpired ():bool {

        return $this->seconds <= 0;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $maxAge = new MaxAge(3600);
     *
     * $maxAge->value();
     * // 3600
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():int {

        return $this->seconds;

    }

    /**
     * ### Gets the Serialized Max-Age Attribute
     *
     * <code>
     * $maxAge = new MaxAge(3600);
     *
     * $maxAge->headerValue();
     * // Max-Age=3600
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized Max-Age attribute.
     */
    public function headerValue ():string {

        return 'Max-Age='.$this->seconds;

    }

}