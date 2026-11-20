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
use FireHub\Adapter\HTTP\Header\Cookie\{
    Domain, Expires, HttpOnly, MaxAge, Pair, Partitioned, Path, SameSite, SameSitePolicy, Secure
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Set-Cookie Header Value
 *
 * Immutable value object representing a single HTTP Set-Cookie header value.
 *
 * Combines a validated cookie name-value pair with optional attributes and enforces security requirements associated
 * with SameSite, Partitioned, and reserved cookie name prefixes.
 *
 * Each instance represents exactly one Set-Cookie header value. Multiple cookies must be transmitted using separate
 * Set-Cookie header fields.
 *
 * <code>
 * $cookie = new SetCookie(
 *     new Pair(
 *         new Name('session'),
 *         new Value('abc123')
 *     ),
 *     path: new Path('/'),
 *     secure: new Secure(),
 *     httpOnly: new HttpOnly(),
 *     sameSite: new SameSite(SameSitePolicy::LAX)
 * );
 *
 * $cookie->value();
 * // session=abc123; Path=/; Secure; HttpOnly; SameSite=Lax
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class SetCookie extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses self::validate() To validate cookie security constraints.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Pair $pair <p>
     * Cookie name-value pair.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Domain $domain [optional] <p>
     * Cookie Domain attribute.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Path $path [optional] <p>
     * Cookie Path attribute.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Expires $expires [optional] <p>
     * Cookie expiration date.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\MaxAge $max_age [optional] <p>
     * Cookie lifetime in seconds.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Secure $secure [optional] <p>
     * Secure attribute restricting cookie transmission to secure connections.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\HttpOnly $http_only [optional] <p>
     * HttpOnly attribute restricting access from client-side scripts.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\SameSite $same_site [optional] <p>
     * SameSite policy controlling cross-site cookie transmission.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Partitioned $partitioned [optional] <p>
     * Partitioned attribute enabling partitioned cookie storage.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If cookie security constraints are
     * violated.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private Pair $pair,
        private ?Domain $domain = null,
        private ?Path $path = null,
        private ?Expires $expires = null,
        private ?MaxAge $max_age = null,
        private ?Secure $secure = null,
        private ?HttpOnly $http_only = null,
        private ?SameSite $same_site = null,
        private ?Partitioned $partitioned = null
    ) {

        $this->validate();

    }

    /**
     * ### Parses an HTTP Set-Cookie Header Value
     *
     * Parses a serialized Set-Cookie header value into a validated cookie pair and its recognized attributes.
     *
     * Attribute names are matched case-insensitively. Unknown attributes are ignored, and repeated recognized
     * attributes use their last occurrence.
     *
     * <code>
     * $cookie = SetCookie::parse(
     *     'session=abc123; Path=/; Secure; HttpOnly; SameSite=Lax'
     * );
     *
     * $cookie->pair()->name()->value(); // session
     * $cookie->path()?->content()->value(); // /
     * $cookie->secure() !== null; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::parse() To parse the cookie name-value pair.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Domain::parse() To parse the Domain attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Path::parse() To parse the Path attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Expires::parse() To parse the Expires attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\MaxAge::parse() To parse the Max-Age attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\SameSite::parse() To parse the SameSite attribute.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the serialized attributes.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize attribute names.
     * @uses \FireHub\Runtime\Arr\Mutation::shift() To remove the cookie pair from the array.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the first occurrence of an attribute separator.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the attribute value.
     *
     * @param non-empty-string $value <p>
     * Serialized Set-Cookie header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie value or security
     * constraints are invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a recognized attribute is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Set-Cookie header value.
     */
    public static function parse (string $value):self {

        $segments = Runtime\Str\SB\Delimiter::explode($value, ';');

        $first = Runtime\Str\SB\Transform::trim(
            Runtime\Arr\Mutation::shift($segments) ?? '', characters: " \t"
        );

        if ($first === '')
            throw new InvalidFieldValueException(
                'An HTTP Set-Cookie header must contain a cookie pair.'
            );

        $pair = Pair::parse($first);

        $domain = null;
        $path = null;
        $expires = null;
        $maxAge = null;
        $secure = null;
        $httpOnly = null;
        $sameSite = null;
        $partitioned = null;

        foreach ($segments as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '') continue;

            $separator = Runtime\Str\SB\Search::firstPosition('=', $segment);

            $name = Runtime\Str\SB\Casing::toLower(
                Runtime\Str\SB\Transform::trim(
                    $separator === false
                        ? $segment
                        : Runtime\Str\SB\Access::part($segment, 0, $separator),
                    characters: " \t"
                )
            );

            switch ($name) {

                case 'domain':
                    $domain = Domain::parse($segment);
                    break;

                case 'path':
                    $path = Path::parse($segment);
                    break;

                case 'expires':
                    $expires = Expires::parse($segment);
                    break;

                case 'max-age':
                    $maxAge = MaxAge::parse($segment);
                    break;

                case 'secure':
                    $secure = new Secure;
                    break;

                case 'httponly':
                    $httpOnly = new HttpOnly;
                    break;

                case 'samesite':
                    $sameSite = SameSite::parse($segment);
                    break;

                case 'partitioned':
                    $partitioned = new Partitioned();
                    break;

            }

        }

        return new self(
            $pair,
            $domain,
            $path,
            $expires,
            $maxAge,
            $secure,
            $httpOnly,
            $sameSite,
            $partitioned
        );

    }

    /**
     * ### Gets the Cookie Pair
     *
     * Returns the validated cookie name-value pair.
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; Secure');
     *
     * $cookie->pair()->name()->value(); // session
     * $cookie->pair()->content()->value(); // abc123
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie\Pair Cookie name-value pair.
     */
    public function pair ():Pair {

        return $this->pair;

    }

    /**
     * ### Gets the Cookie Domain
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; Domain=example.com');
     *
     * $cookie->domain()?->content()->value(); // example.com
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Domain Cookie Domain attribute.
     */
    public function domain ():?Domain {

        return $this->domain;

    }

    /**
     * ### Gets the Cookie Path
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; Path=/admin');
     *
     * $cookie->path()?->content()->value(); // /admin
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Path Cookie Path attribute.
     */
    public function path ():?Path {

        return $this->path;

    }

    /**
     * ### Gets the Cookie Expiration
     *
     * Returns the optional Expires attribute.
     *
     * <code>
     * $cookie = SetCookie::parse(
     *     'session=abc123; Expires=Wed, 21 Oct 2026 07:28:00 GMT'
     * );
     *
     * $cookie->expires()?->value();
     * // Expires=Wed, 21 Oct 2026 07:28:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Expires Cookie Expires attribute.
     */
    public function expires ():?Expires {

        return $this->expires;

    }

    /**
     * ### Gets the Cookie Maximum Age
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; Max-Age=3600');
     *
     * $cookie->maxAge()?->seconds(); // 3600
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\MaxAge Cookie Max-Age attribute.
     */
    public function maxAge ():?MaxAge {

        return $this->max_age;

    }

    /**
     * ### Gets the Secure Attribute
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; Secure');
     *
     * $cookie->secure() !== null; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Secure Cookie Secure attribute.
     */
    public function secure ():?Secure {

        return $this->secure;

    }

    /**
     * ### Gets the HttpOnly Attribute
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; HttpOnly');
     *
     * $cookie->httpOnly() !== null; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\HttpOnly Cookie HttpOnly attribute.
     */
    public function httpOnly ():?HttpOnly {

        return $this->http_only;

    }

    /**
     * ### Gets the SameSite Attribute
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; SameSite=Strict');
     *
     * $cookie->sameSite()?->policy(); // SameSitePolicy::STRICT
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\SameSite Cookie SameSite attribute.
     */
    public function sameSite ():?SameSite {

        return $this->same_site;

    }

    /**
     * ### Gets the Partitioned Attribute
     *
     * <code>
     * $cookie = SetCookie::parse('session=abc123; Secure; Partitioned');
     *
     * $cookie->partitioned() !== null; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Partitioned Cookie Partitioned attribute.
     */
    public function partitioned ():?Partitioned {

        return $this->partitioned;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $cookie = SetCookie::parse(
     *     'session=abc123; Path=/; Secure; HttpOnly; SameSite=Lax'
     * );
     *
     * $cookie->value();
     * // session=abc123; Path=/; Secure; HttpOnly; SameSite=Lax
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::value() To serialize the cookie pair.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the cookie attributes.
     *
     * @return non-empty-string Serialized Set-Cookie header value.
     */
    public function value ():string {

        $parts = [$this->pair->value()];

        foreach ([
            $this->expires, $this->max_age, $this->domain, $this->path,
            $this->secure, $this->http_only, $this->same_site, $this->partitioned
        ] as $attribute)
            if ($attribute !== null)
                $parts[] = $attribute->value();

        /** @var non-empty-string */
        return Runtime\Str\SB\Delimiter::implode($parts, '; ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{
     *     \FireHub\Adapter\HTTP\Header\Cookie\Pair,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\Domain,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\Path,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\Expires,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\MaxAge,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\Secure,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\HttpOnly,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\SameSite,
     *     null|\FireHub\Adapter\HTTP\Header\Cookie\Partitioned
     * } Components used for comparison.
     */
    protected function comparisonValue ():array {

        return [
            $this->pair, $this->domain, $this->path, $this->expires, $this->max_age,
            $this->secure, $this->http_only, $this->same_site, $this->partitioned
        ];

    }

    /**
     * ### Validates Cookie Security Constraints
     *
     * Enforces relationships between cookie attributes and restrictions associated with reserved cookie name prefixes.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Pair::name() To retrieve the cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\SameSite::policy() To retrieve the SameSite policy.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Path::isRoot() To validate root path requirements.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check for reserved cookie name prefixes.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If cookie security constraints are
     * violated.
     *
     * @return void
     */
    private function validate ():void {

        $name = $this->pair->name()->value();

        if (
            $this->same_site?->policy() === SameSitePolicy::NONE
            && $this->secure === null
        ) throw new InvalidFieldValueException(
            'SameSite=None requires the Secure attribute.'
        );

        if (
            $this->partitioned !== null
            && $this->secure === null
        ) throw new InvalidFieldValueException(
            'Partitioned cookies require the Secure attribute.'
        );

        if (
            Runtime\Str\SB\Search::startsWith('__Secure-', $name)
            && $this->secure === null
        ) throw new InvalidFieldValueException(
            'Cookies with the __Secure- prefix require Secure.'
        );

        if (Runtime\Str\SB\Search::startsWith('__Host-', $name)) {

            if ($this->secure === null)
                throw new InvalidFieldValueException(
                    'Cookies with the __Host- prefix require Secure.'
                );

            if ($this->domain !== null)
                throw new InvalidFieldValueException(
                    'Cookies with the __Host- prefix must not specify Domain.'
                );

            if ($this->path?->isRoot() !== true)
                throw new InvalidFieldValueException(
                    'Cookies with the __Host- prefix require Path=/.'
                );

        }

        if (
            Runtime\Str\SB\Search::startsWith('__Http-', $name)
            && (
                $this->secure === null
                || $this->http_only === null
            )
        ) throw new InvalidFieldValueException(
            'Cookies with the __Http- prefix require Secure and HttpOnly.'
        );

        if (Runtime\Str\SB\Search::startsWith('__Host-Http-', $name)) {

            if (
                $this->secure === null
                || $this->http_only === null
            ) throw new InvalidFieldValueException(
                'Cookies with the __Host-Http- prefix require Secure and HttpOnly.'
            );

            if ($this->domain !== null)
                throw new InvalidFieldValueException(
                    'Cookies with the __Host-Http- prefix must not specify Domain.'
                );

            if ($this->path?->isRoot() !== true)
                throw new InvalidFieldValueException(
                    'Cookies with the __Host-Http- prefix require Path=/.'
                );

        }

    }

}