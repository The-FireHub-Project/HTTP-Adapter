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

namespace FireHub\Adapter\HTTP\Header\Cookie;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Cookie;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Request Cookies
 *
 * Represents an immutable collection of HTTP cookie pairs received through the Cookie request header field.
 *
 * Cookie pairs are stored in their original order, and duplicate cookie names are preserved.
 *
 * Cookie names are case-sensitive.
 *
 * This class represents request cookies only. Response cookie attributes such as Path, Domain, Secure, HttpOnly, and
 * SameSite are not supported.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Cookie;
 * use FireHub\Adapter\HTTP\Header\Cookie\Request;
 * use FireHub\Adapter\HTTP\Header\Cookie\Name;
 *
 * $cookies = Request::parse(
 *     'session=abc123; theme=dark; session=xyz'
 * );
 *
 * $cookies->get(new Name('session'))?->content()->value();
 * // abc123
 *
 * count($cookies->all(new Name('session')));
 * // 2
 *
 * $cookies->value();
 * // session=abc123; theme=dark; session=xyz
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Request extends ValueObject {

    /**
     * ### HTTP Request Cookies
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Cookie>
     */
    private array $cookies;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the cookie list.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie ...$cookies <p>
     * Cookie pairs to include in the collection.
     * </p>
     *
     * @return void
     */
    public function __construct (Cookie ...$cookies) {

        $this->cookies = Runtime\Arr\Access::values($cookies);

    }

    /**
     * ### Parses Request Cookies
     *
     * Parses the serialized value of an HTTP Cookie request header.
     *
     * Semicolons separate cookie pairs. Optional spaces and horizontal tabs surrounding each pair are removed.
     *
     * Duplicate cookie names and their original order are preserved.
     *
     * An empty input produces an empty collection.
     *
     * Empty segments and malformed cookie pairs are rejected.
     *
     * <code>
     * $cookies = Request::parse(
     *     'session=abc123; theme=dark'
     * );
     *
     * $cookies->count();
     * // 2
     *
     * $cookies = Request::parse(
     *     'session=abc; session=xyz'
     * );
     *
     * count($cookies->all(new Name('session')));
     * // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie::parse() To parse individual cookie pairs.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the header value.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     *
     * @param string $value <p>
     * Serialized HTTP Cookie header field value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header contains an empty
     * segment or malformed cookie pair.
     * @throws \FireHub\Core\Exception\FireHubException If a cookie validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a validation exception is not a FireHubException.
     *
     * @return self Parsed request cookie collection.
     */
    public static function parse (string $value):self {

        if ($value === '') return new self();

        $cookies = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ';') as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '')
                throw new InvalidFieldValueException(
                    'HTTP Cookie header contains an empty cookie pair.'
                );

            $cookies[] = Cookie::parse($segment);

        }

        return new self(...$cookies);

    }

    /**
     * ### Gets All Cookie Pairs
     *
     * Returns every cookie pair in its original order.
     *
     * Duplicate cookie names are preserved.
     *
     * <code>
     * $cookies = Request::parse(
     *     'session=abc; theme=dark'
     * );
     *
     * foreach ($cookies->cookies() as $cookie)
     *     echo $cookie->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Cookie> All cookie pairs.
     */
    public function cookies ():array {

        return $this->cookies;

    }

    /**
     * ### Gets the First Cookie by Name
     *
     * Returns the first cookie matching the provided name.
     *
     * Cookie names are compared case-sensitive.
     *
     * Returns null when no matching cookie exists.
     *
     * <code>
     * $cookies = Request::parse(
     *     'session=abc; session=xyz'
     * );
     *
     * $cookies->get(new Name('session'))?->content()->value();
     * // abc
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie::name() To retrieve each cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Name::value() To compare cookie names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Name $name <p>
     * Name of the cookie to retrieve.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie|null First matching cookie, or null if not found.
     */
    public function get (Name $name):?Cookie {

        foreach ($this->cookies as $cookie)
            if ($cookie->name()->value() === $name->value())
                return $cookie;

        return null;

    }

    /**
     * ### Gets All Cookies by Name
     *
     * Returns every cookie matching the provided name.
     *
     * The original order of matching cookies is preserved.
     *
     * <code>
     * $cookies = Request::parse(
     *     'session=abc; theme=dark; session=xyz'
     * );
     *
     * $matches = $cookies->all(new Name('session'));
     *
     * count($matches);
     * // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie::name() To retrieve each cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Name::value() To compare cookie names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Name $name <p>
     * Name of the cookies to retrieve.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Cookie> Matching cookies.
     */
    public function all (Name $name):array {

        $matches = [];

        foreach ($this->cookies as $cookie)
            if ($cookie->name()->value() === $name->value())
                $matches[] = $cookie;

        return $matches;

    }

    /**
     * ### Gets the Cookie Count
     *
     * Returns the total number of cookie pairs, including cookies with duplicate names.
     *
     * <code>
     * $cookies = Request::parse(
     *     'session=abc; session=xyz'
     * );
     *
     * $cookies->count();
     * // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count cookie pairs.
     *
     * @return non-negative-int Number of cookie pairs.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->cookies);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $cookies = Request::parse(
     *     'session=abc;theme=dark'
     * );
     *
     * $cookies->value();
     * // session=abc; theme=dark
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie::value() To serialize individual cookie pairs.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join serialized cookie pairs.
     */
    public function value ():string {

        $values = [];

        foreach ($this->cookies as $cookie)
            $values[] = $cookie->value();

        return Runtime\Str\SB\Delimiter::implode($values, '; ');

    }

}