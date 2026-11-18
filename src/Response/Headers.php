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

use FireHub\Adapter\HTTP\Message\Headers as MessageHeaders;
use FireHub\Adapter\HTTP\Header\ {
    Cookie\Response\Cookie as ResponseCookie,
    Name, Value
};

/**
 * ### Represents immutable HTTP response headers
 *
 * Provides response-specific header operations while delegating header storage and case-insensitive matching to the
 * underlying HTTP message header collection.
 *
 * Every modification produces a new response header collection.
 * @since 1.0.0
 */
final readonly class Headers extends MessageHeaders {

    /**
     * ### Appends an HTTP Set-Cookie Header
     *
     * Appends a response cookie as an individual Set-Cookie header field.
     *
     * Existing Set-Cookie headers are preserved, including cookies with the same name.
     *
     * Each cookie is serialized into a separate header field because Set-Cookie values must not be combined into a
     * comma-separated list.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Response\Headers;
     *
     * $headers = Headers::empty()
     *     ->withCookie($sessionCookie)
     *     ->withCookie($languageCookie);
     *
     * $headers->get(new Name('Set-Cookie'));
     * // Two separate header values.
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\Cookie::value() To serialize the response cookie.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::add() To append the header field.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Response\Cookie $cookie <p>
     * Response cookie to append.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static New response headers collection containing the appended cookie.
     */
    public function addCookie (ResponseCookie $cookie):self {

        return $this->add(
            new Name('Set-Cookie'),
            new Value($cookie->value())
        );

    }

}