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
use FireHub\Adapter\HTTP\Cookie\Request as RequestCookie;
use FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Cookie request header value
 *
 * Preserves cookie order and duplicate cookie names.
 *
 * Each instance represents the value of one Cookie header field.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Request extends ValueObject {

    /**
     * ### Request cookies
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Cookie\Request<string>>
     */
    private array $cookies;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To remove the keys from the cookies array.
     *
     * @param \FireHub\Adapter\HTTP\Cookie\Request<string> ...$cookies <p>
     * Request cookies.
     * </p>
     *
     * @return void
     */
    public function __construct (RequestCookie ...$cookies) {

        $this->cookies = Runtime\Arr\Access::values($cookies);

    }

    /**
     * ### Parses an HTTP Cookie request header
     *
     * Invalid cookie pairs are silently skipped. Preserves the order and duplicate names of valid cookies.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the Cookie header value into cookie pairs.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim cookie pair values.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the position of the first '=' character in a
     * cookie pair.
     * @uses \FireHub\Adapter\HTTP\Cookie\Request::parse() To parse a single cookie pair.
     *
     * @param string $value <p>
     * Raw Cookie header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed request cookies.
     */
    public static function parse (string $value):self {

        $cookies = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ';') as $pair) {

            $pair = Runtime\Str\SB\Transform::trim($pair, characters: " \t");

            if ($pair === '') continue;

            $position = Runtime\Str\SB\Search::firstPosition('=', $pair);

            if ($position === false) continue;

            $name = Runtime\Str\SB\Transform::trim(
                Runtime\Str\SB\Access::part($pair, 0, $position),
                characters: " \t"
            );

            $cookie_value = Runtime\Str\SB\Transform::trim(
                Runtime\Str\SB\Access::part($pair, $position + 1),
                characters: " \t"
            );

            if ($name === '') continue;

            try {

                $cookies[] = new RequestCookie(
                    $name,
                    $cookie_value
                );

            } catch (InvalidCookieNameException) {

                continue;

            }

        }

        return new self(...$cookies);

    }

    /**
     * ### Gets all request cookies
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Cookie\Request<string>> Request cookies.
     */
    public function cookies ():array {

        return $this->cookies;

    }

    /**
     * ### Gets all cookies with the specified name
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Cookie\Request::name() To get the cookie name.
     *
     * @param non-empty-string $name <p>
     * Cookie name.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Cookie\Request<string>> Matching cookies.
     */
    public function get (string $name):array {

        $matches = [];

        foreach ($this->cookies as $cookie)
            if ($cookie->name() === $name)
                $matches[] = $cookie;

        return $matches;

    }

    /**
     * ### Checks whether a cookie exists
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Cookie\Request::name() To get the cookie name.
     *
     * @param non-empty-string $name <p>
     * Cookie name.
     * </p>
     *
     * @return bool True if a matching cookie exists.
     */
    public function has (string $name):bool {

        foreach ($this->cookies as $cookie)
            if ($cookie->name() === $name)
                return true;

        return false;

    }

    /**
     * ### Returns a header with an additional cookie
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Cookie\Request<string> $cookie <p>
     * Cookie to append.
     * </p>
     *
     * @return self New header instance.
     */
    public function with (RequestCookie $cookie):self {

        return new self(
            ...[...$this->cookies, $cookie]
        );

    }

    /**
     * ### Returns a header without cookies matching the specified name
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Cookie\Request::name() To get the cookie name.
     *
     * @param non-empty-string $name <p>
     * Cookie name.
     * </p>
     *
     * @return self New header instance.
     */
    public function without (string $name):self {

        $cookies = [];

        foreach ($this->cookies as $cookie) {

            if ($cookie->name() !== $name)
                $cookies[] = $cookie;

        }

        return new self(...$cookies);

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Cookie\Request::name() To get the cookie name.
     * @uses \FireHub\Adapter\HTTP\Cookie\Request::value() To get the cookie value.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join cookie pairs.
     */
    public function value ():string {

        $values = [];

        foreach ($this->cookies as $cookie)
            $values[] = $cookie->name() . '=' . $cookie->value();

        return Runtime\Str\SB\Delimiter::implode(
            $values,
            '; '
        );

    }

}