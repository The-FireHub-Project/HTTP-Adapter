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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Cookie\Pair;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Cookie Header
 *
 * Immutable collection of HTTP cookie name-value pairs.
 *
 * Represents the Cookie request header. Cookie pairs are separated by semicolons, and their original order is
 * preserved.
 *
 * Duplicate cookie names are allowed and compared case-sensitively.
 *
 * <code>
 * $cookies = Cookie::parse(
 *     'session=abc123; theme=dark'
 * );
 *
 * $cookies->count(); // 2
 * $cookies->value(); // session=abc123; theme=dark
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Cookies extends ValueObject {

    /**
     * ### HTTP Cookie Pairs
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Cookie\Pair>
     */
    private array $cookies;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To preserve insertion order.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Pair ...$cookies <p>
     * Cookie pairs to store.
     * </p>
     *
     * @return void
     */
    public function __construct (Pair ...$cookies) {

        $this->cookies = Runtime\Arr\Access::values($cookies);

    }

    /**
     * ### Parses an HTTP Cookie Header
     *
     * Parses semicolon-separated cookie pairs.
     *
     * Optional whitespace surrounding individual pairs is ignored. Empty segments are rejected.
     *
     * <code>
     * $cookies = Cookie::parse(
     *     'session=abc123; theme=dark'
     * );
     *
     * $cookies->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::parse() To parse cookie pairs.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split cookie pairs.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     *
     * @param string $value <p>
     * Serialized Cookie header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a cookie pair is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If a cookie name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Cookie header.
     */
    public static function parse (string $value):self {

        if ($value === '') return new self;

        $cookies = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ';') as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '')
                throw new InvalidFieldValueException(
                    'An HTTP Cookie header cannot contain an empty cookie pair.'
                );

            $cookies[] = Pair::parse($segment);

        }

        return new self(...$cookies);

    }

    /**
     * ### Checks Whether a Cookie Exists
     *
     * Checks whether at least one cookie pair has the specified name.
     *
     * Cookie names are compared case-sensitive.
     *
     * <code>
     * $cookies = Cookie::parse('session=abc123; theme=dark');
     *
     * $cookies->has(new Name('session')); // true
     * $cookies->has(new Name('Session')); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::name() To retrieve cookie names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare cookie names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Cookie name to search for.
     * </p>
     *
     * @return bool True if a matching cookie exists.
     */
    public function has (Name $name):bool {

        foreach ($this->cookies as $cookie)
            if ($cookie->name()->equals($name))
                return true;

        return false;

    }

    /**
     * ### Gets Cookies by Name
     *
     * Returns all cookie pairs matching the specified name.
     *
     * Duplicate names are preserved in their original order.
     *
     * <code>
     * $cookies = Cookie::parse(
     *     'session=abc123; theme=dark; session=xyz'
     * );
     *
     * $matches = $cookies->get(new Name('session'));
     *
     * count($matches); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::name() To retrieve cookie names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare cookie names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Cookie name to search for.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Cookie\Pair> Matching cookie pairs.
     */
    public function get (Name $name):array {

        $cookies = [];

        foreach ($this->cookies as $cookie)
            if ($cookie->name()->equals($name))
                $cookies[] = $cookie;

        return $cookies;

    }

    /**
     * ### Appends an HTTP Cookie Pair
     *
     * Returns a new Cookie collection containing the additional pair.
     *
     * <code>
     * $cookies = Cookie::parse('session=abc123');
     *
     * $updated = $cookies->add(
     *     new Pair(
     *         new Name('theme'),
     *         new Value('dark')
     *     )
     * );
     *
     * $updated->value(); // session=abc123; theme=dark
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Pair $cookie <p>
     * Cookie pair to append.
     * </p>
     *
     * @return self Updated Cookie collection.
     */
    public function add (Pair $cookie):self {

        return new self(...[
            ...$this->cookies,
            $cookie
        ]);

    }

    /**
     * ### Removes Cookies by Name
     *
     * Removes all cookie pairs matching the specified name.
     *
     * <code>
     * $cookies = Cookie::parse(
     *     'session=abc123; theme=dark; session=xyz'
     * );
     *
     * $updated = $cookies->remove(new Name('session'));
     *
     * $updated->value(); // theme=dark
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::name() To retrieve cookie names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare cookie names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Cookie name to remove.
     * </p>
     *
     * @return self Updated Cookie collection.
     */
    public function remove (Name $name):self {

        $cookies = [];

        foreach ($this->cookies as $cookie)
            if (!$cookie->name()->equals($name))
                $cookies[] = $cookie;

        return new self(...$cookies);

    }

    /**
     * ### Returns the Number of Cookie Pairs
     *
     * <code>
     * $cookies = Cookie::parse('session=abc123; theme=dark');
     *
     * $cookies->count(); // 2
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
     * ### Returns All Cookie Pairs
     *
     * Returns cookie pairs in their original order.
     *
     * <code>
     * $cookies = Cookie::parse('session=abc123; theme=dark');
     *
     * foreach ($cookies->all() as $cookie)
     *     echo $cookie->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Cookie\Pair> Stored cookie pairs.
     */
    public function all ():array {

        return $this->cookies;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $cookies = Cookie::parse(
     *     'session=abc123;theme=dark'
     * );
     *
     * $cookies->value(); // session=abc123; theme=dark
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::value() To serialize cookie pairs.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join serialized cookie pairs.
     */
    public function value ():string {

        $values = [];

        foreach ($this->cookies as $cookie)
            $values[] = $cookie->value();

        return Runtime\Str\SB\Delimiter::implode($values, '; ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Cookie\Pair> Cookie pairs used for comparison.
     */
    protected function comparisonValue ():array {

        return $this->cookies;

    }

}