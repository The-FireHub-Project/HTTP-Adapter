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
    Cookie\Response\Cookie as ResponseCookie, Cookie\Response\Domain as CookieDomain,
    Cookie\Response\Path as CookiePath, Cookie\Name as CookieName,
    Name, Value
};
use FireHub\Runtime;

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

    /**
     * ### Removes HTTP Response Cookies
     *
     * Removes Set-Cookie header fields matching the specified cookie name.
     *
     * Optional Path and Domain parameters restrict which cookies are removed. Omitted parameters act as wildcards.
     *
     * Cookie values and other attributes are ignored during matching.
     *
     * <code>
     * $headers = $headers->removeCookie(
     *     new CookieName('session'),
     *     path: new Path('/admin')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve existing Set-Cookie headers.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace Set-Cookie headers.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::without() To remove all Set-Cookie headers.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\Cookie::parse() To parse response cookies.
     * @uses \FireHub\Runtime\Arr\Structure::slice() To remove matching cookies.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Name $name <p>
     * Case-sensitive cookie name to remove.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Path $path [optional] <p>
     * Optional path filter. Null matches any path.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Domain $domain [optional] <p>
     * Optional domain filter. Null matches any domain.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If an existing cookie is malformed.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If an expiration date is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated response headers collection.
     */
    public function removeCookie (CookieName $name, ?CookiePath $path = null, ?CookieDomain $domain = null):self {

        $header = new Name('Set-Cookie');
        $values = [];

        foreach ($this->get($header) as $value) {

            $cookie = ResponseCookie::parse($value->value());

            if (
                ($domain === null || $cookie->domain()?->value() === $domain->value())
                && ($path === null || $cookie->path()?->value() === $path->value())
                && $cookie->cookie()->name()->value() === $name->value()
            ) continue;

            $values[] = $value;

        }

        if ($values === [])
            return $this->without($header);

        return $this->with(
            $header,
            $values[0],
            ...Runtime\Arr\Structure::slice($values, 1)
        );

    }

}