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
    Allow, Name, SetCookie, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;

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
     * ### Sets the HTTP Allow header
     *
     * Replaces all existing Allow header fields with the supplied collection of permitted HTTP methods.
     *
     * The original response header collection remains unchanged.
     *
     * <code>
     * $headers = Headers::empty()->withAllow(
     *     new Allow(
     *         Standard::GET,
     *         Standard::HEAD,
     *         Standard::POST
     *     )
     * );
     *
     * $headers->getAllow()?->value();
     *
     * // GET, HEAD, POST
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Allow::value() To serialize the allowed methods.
     * @uses self::with() To replace the existing Allow header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Allow $allow <p>
     * Allowed HTTP request methods.
     * </p>
     *
     * @return self Updated HTTP response headers.
     */
    public function withAllow (Allow $allow):self {

        return $this->with(
            new Name('Allow'),
            new Value($allow->value())
        );

    }

    /**
     * ### Gets the HTTP Allow header
     *
     * Retrieves the Allow header and parses its permitted HTTP methods.
     *
     * Returns null when the Allow header is absent.
     *
     * <code>
     * $headers = Headers::empty()->withAllow(
     *     new Allow(
     *         Standard::GET,
     *         Standard::POST
     *     )
     * );
     *
     * $allow = $headers->getAllow();
     *
     * $allow?->contains(Standard::GET);
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::get() To retrieve the Allow header.
     * @uses \FireHub\Adapter\HTTP\Header\Allow::parse() To parse its value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If the method token is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Allow Parsed Allow header or null if absent.
     */
    public function getAllow ():?Allow {

        $values = $this->get(new Name('Allow'));

        if ($values === [])
            return null;

        $methods = [];

        foreach ($values as $value) {

            $parsed = Allow::parse($value->value());

            foreach ($parsed->methods() as $method)
                $methods[] = $method;

        }

        return new Allow(...$methods);

    }

    /**
     * ### Adds an HTTP Set-Cookie Header
     *
     * Appends a Set-Cookie header field without replacing any existing Set-Cookie fields.
     *
     * Each cookie is serialized and stored as a separate header field. Multiple Set-Cookie values are never combined
     * into a comma-separated list.
     *
     * The original response header collection remains unchanged.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->addSetCookie(
     *         SetCookie::parse('session=abc123; Path=/; Secure; HttpOnly')
     *     )
     *     ->addSetCookie(
     *         SetCookie::parse('theme=dark; Path=/')
     *     );
     *
     * $headers->getSetCookies();
     * // [SetCookie('session=abc123; ...'), SetCookie('theme=dark; ...')]
     *
     * $headers->get(new Name('Set-Cookie'));
     * // [Value('session=abc123; ...'), Value('theme=dark; ...')]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\SetCookie::value() To serialize the Set-Cookie header.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::add() To append the header field.
     *
     * @param \FireHub\Adapter\HTTP\Header\SetCookie $cookie <p>
     * Set-Cookie header containing the cookie pair and its attributes.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized header value is
     * invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated response headers.
     */
    public function addCookie (SetCookie $cookie):self {

        return $this->add(
            new Name('Set-Cookie'),
            new Value($cookie->value())
        );

    }

    /**
     * ### Gets All HTTP Set-Cookie Headers
     *
     * Retrieves all Set-Cookie header field values and parses each field independently.
     *
     * Parsed cookies are returned as a list while preserving their original order and duplicate cookie names.
     *
     * Returns an empty list when the Set-Cookie header is absent.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Set-Cookie'), new Value('session=abc123; Path=/; Secure'))
     *     ->add(new Name('Set-Cookie'), new Value('theme=dark; Path=/'))
     *     ->add(new Name('Set-Cookie'), new Value('session=xyz789; Path=/admin'));
     *
     * $cookies = $headers->getSetCookies();
     *
     * count($cookies); // 3
     *
     * $cookies[0]->pair()->name()->value(); // session
     * $cookies[1]->pair()->name()->value(); // theme
     * $cookies[2]->pair()->name()->value(); // session
     *
     * // Missing header.
     * Headers::empty()->getSetCookies(); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Set-Cookie header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve each field value.
     * @uses \FireHub\Adapter\HTTP\Header\SetCookie::parse() To parse individual Set-Cookie header fields.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a cookie value or security
     * constraint is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If a cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a cookie attribute is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\SetCookie> Parsed Set-Cookie headers.
     */
    public function getCookies ():array {

        $cookies = [];

        foreach ($this->get(new Name('Set-Cookie')) as $value) {

            $content = $value->value();

            if ($content === '')
                throw new InvalidFieldValueException(
                    'The Set-Cookie header value must not be empty.'
                );

            $cookies[] = SetCookie::parse($content);

        }

        return $cookies;

    }

}