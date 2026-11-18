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
use FireHub\Core\Meta\Enum\Side;
use FireHub\Adapter\HTTP\Header\Cookie as BaseCookie;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Foundation\Temporal\DateTime;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Response Cookie
 *
 * The Cookie class represents a complete HTTP response cookie, including its name, value, and optional attributes
 * used in the Set-Cookie response header.
 *
 * A response cookie may define expiration rules, domain and path restrictions, transport security requirements,
 * JavaScript access restrictions, cross-site policies, and partitioned storage behavior.
 *
 * The class is immutable. All cookie attributes are validated during construction to ensure that the resulting
 * cookie satisfies the applicable HTTP cookie constraints.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Cookie\Response\Cookie;
 * use FireHub\Adapter\HTTP\Header\Cookie\Response\Path;
 * use FireHub\Adapter\HTTP\Header\Cookie\Response\MaxAge;
 * use FireHub\Adapter\HTTP\Header\Cookie\Response\SameSite;
 *
 * $cookie = new Cookie(
 *     cookie: $baseCookie,
 *     path: new Path('/'),
 *     max_age: new MaxAge(3600),
 *     secure: true,
 *     http_only: true,
 *     same_site: SameSite::LAX
 * );
 *
 * $cookie->value();
 * // session=abc; Path=/; Max-Age=3600; Secure; HttpOnly; SameSite=Lax
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Cookie extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses self::validate() To validate cookie attributes and reserved name prefixes.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie $cookie <p>
     * The base cookie containing its name and value.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Domain $domain [optional] <p>
     * Optional domain restriction. When omitted, the cookie is restricted to the originating host.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Path $path [optional] <p>
     * Optional path restriction. When omitted, the user agent determines the default cookie path.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Expires $expires [optional] <p>
     * Optional absolute expiration date. When omitted together with Max-Age, the cookie is a session cookie.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Response\MaxAge $max_age [optional] <p>
     * Optional cookie lifetime in seconds. When both Max-Age and Expires are present, Max-Age takes precedence.
     * </p>
     * @param bool $secure [optional] <p>
     * Whether the Secure attribute is enabled.
     * </p>
     * @param bool $http_only [optional] <p>
     * Whether the HttpOnly attribute is enabled.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Cookie\Response\SameSite $same_site [optional] <p>
     * Optional SameSite policy controlling cross-site cookie transmission.
     * </p>
     * @param bool $partitioned [optional] <p>
     * Whether partitioned cookie storage is requested.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie attributes violate
     * security requirements or reserved cookie name prefix restrictions.
     *
     * @return void
     */
    public function __construct (
        private BaseCookie $cookie,
        private ?Domain $domain = null,
        private ?Path $path = null,
        private ?Expires $expires = null,
        private ?MaxAge $max_age = null,
        private bool $secure = false,
        private bool $http_only = false,
        private ?SameSite $same_site = null,
        private bool $partitioned = false
    ) {

        $this->validate();

    }

    /**
     * ### Parses an HTTP Response Cookie
     *
     * Parses a serialized Set-Cookie header field value into an immutable response cookie object.
     *
     * Recognizes the Domain, Path, Expires, Max-Age, Secure, HttpOnly, SameSite, and Partitioned attributes.
     *
     * Attribute names are matched case-insensitively. Unknown extension attributes are ignored.
     *
     * <code>
     * $cookie = Cookie::parse(
     *     'session=abc123; Path=/; Secure; HttpOnly; SameSite=Lax'
     * );
     *
     * $cookie->cookie()->name()->value();
     * // session
     *
     * $cookie->path()?->value();
     * // /
     *
     * $cookie->isSecure();
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie::parse() To parse the cookie pair.
     * @uses \FireHub\Foundation\Temporal\DateTime::fromFormat() To parse expiration dates.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the cookie attributes.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim attribute values.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert attribute names to lowercase.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate values.
     * @uses \FireHub\Runtime\Arr\Mutation::shift() To remove the cookie pair from the attribute list.
     * @uses \FireHub\Runtime\Arr\Structure::pad() To pad attribute values with null.
     * @uses \FireHub\Runtime\Filter::value() To parse integer values.
     *
     * @param string $value <p>
     * Serialized Set-Cookie header field value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a supported attribute is malformed.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the expiration date is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP response cookie.
     */
    public static function parse (string $value):self {

        $segments = Runtime\Str\SB\Delimiter::explode($value, ';');

        $pair = Runtime\Str\SB\Transform::trim(Runtime\Arr\Mutation::shift($segments) ?? '', characters: " \t");

        if ($pair === '')
            throw new InvalidFieldValueException(
                'HTTP Set-Cookie header requires a cookie pair.'
            );

        $cookie = BaseCookie::parse($pair);

        $domain = null;
        $path = null;
        $expires = null;
        $max_age = null;
        $secure = false;
        $http_only = false;
        $same_site = null;
        $partitioned = false;

        foreach ($segments as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '') continue;

            /**
             * @var non-empty-string $name
             * @var null|non-empty-string $content
             */
            [$name, $content] = Runtime\Arr\Structure::pad( // @phpstan-ignore-line
                Runtime\Str\SB\Delimiter::explode($segment, '=', 2),
                2,
                null
            );

            $name = Runtime\Str\SB\Casing::toLower(Runtime\Str\SB\Transform::trim($name, characters: " \t"));
            $content = $content !== null
                ? Runtime\Str\SB\Transform::trim($content, characters: " \t")
                : null;

            switch ($name) {

                case 'domain':

                    if ($content === null || $content === '')
                        throw new InvalidFieldValueException(
                            'Invalid Set-Cookie Domain attribute.'
                        );

                    /** @var non-empty-string $domain_value */
                    $domain_value = Runtime\Str\SB\Transform::trim($content, Side::LEFT, '.');

                    $domain = new Domain(
                        $domain_value
                    );

                    break;

                case 'path':

                    if ($content === null || $content === '')
                        throw new InvalidFieldValueException(
                            'Invalid Set-Cookie Path attribute.'
                        );

                    $path = new Path($content);

                    break;

                case 'expires':

                    if ($content === null || $content === '')
                        throw new InvalidFieldValueException(
                            'Invalid Set-Cookie Expires attribute.'
                        );

                    $expires = new Expires(
                        DateTime::fromFormat(
                            $content,
                            '!D, d M Y H:i:s \G\M\T'
                        )
                    );

                    break;

                case 'max-age':

                    if (
                        $content === null
                        || !Runtime\Str\SB\Regex::match('/\A-?(?:0|[1-9][0-9]*)\z/', $content)
                    ) throw new InvalidFieldValueException(
                        'Invalid Set-Cookie Max-Age attribute.'
                    );

                    /** @var int|false $seconds */
                    $seconds = Runtime\Filter::value(
                        $content,
                        FILTER_VALIDATE_INT
                    );

                    if ($seconds === false)
                        throw new InvalidFieldValueException(
                            'Set-Cookie Max-Age exceeds the supported integer range.'
                        );

                    $max_age = new MaxAge($seconds);

                    break;

                case 'secure':

                    $secure = true;

                    break;

                case 'httponly':

                    $http_only = true;

                    break;

                case 'samesite':

                    $same_site = match (Runtime\Str\SB\Casing::toLower($content ?? '')) {
                        'strict' => SameSite::STRICT,
                        'lax' => SameSite::LAX,
                        'none' => SameSite::NONE,
                        default => throw new InvalidFieldValueException(
                            'Invalid Set-Cookie SameSite attribute.'
                        )
                    };

                    break;

                case 'partitioned':

                    $partitioned = true;

                    break;

            }

        }

        return new self(
            $cookie,
            $domain,
            $path,
            $expires,
            $max_age,
            $secure,
            $http_only,
            $same_site,
            $partitioned
        );

    }

    /**
     * ### Gets the Base Cookie
     *
     * Returns the cookie containing its name and value without response-specific attributes.
     *
     * <code>
     * $baseCookie = $cookie->cookie();
     *
     * $baseCookie->value();
     * // session=abc
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie The base cookie.
     */
    public function cookie ():BaseCookie {

        return $this->cookie;

    }

    /**
     * ### Gets the Cookie Domain
     *
     * Returns the explicitly configured domain restriction.
     *
     * A null value indicates that no Domain attribute will be emitted, allowing the user agent to apply
     * host-only cookie behavior.
     *
     * <code>
     * $domain = $cookie->domain();
     *
     * $domain?->value();
     * // example.com or null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Domain The cookie domain, or null if unspecified.
     */
    public function domain ():?Domain {

        return $this->domain;

    }

    /**
     * ### Gets the Cookie Path
     *
     * Returns the explicitly configured path restriction.
     *
     * <code>
     * $path = $cookie->path();
     *
     * $path?->value();
     * // / or null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Path The cookie path, or null if unspecified.
     */
    public function path ():?Path {

        return $this->path;

    }

    /**
     * ### Gets the Cookie Expiration Date
     *
     * Returns the absolute expiration date represented by the Expires attribute.
     *
     * <code>
     * $expires = $cookie->expires();
     *
     * $expires?->headerValue();
     * // Expires=Wed, 21 Oct 2026 07:28:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Response\Expires The expiration date, or null if unspecified.
     */
    public function expires ():?Expires {

        return $this->expires;

    }

    /**
     * ### Gets the Cookie Maximum Age
     *
     * Returns the configured cookie lifetime in seconds.
     *
     * <code>
     * $maxAge = $cookie->maxAge();
     *
     * $maxAge?->value();
     * // 3600
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Response\MaxAge The maximum age, or null if unspecified.
     */
    public function maxAge ():?MaxAge {

        return $this->max_age;

    }

    /**
     * ### Checks Whether the Cookie Is Secure
     *
     * Determines whether the Secure attribute is enabled.
     *
     * <code>
     * $cookie->isSecure();
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the Secure attribute is enabled, otherwise false.
     */
    public function isSecure ():bool {

        return $this->secure;

    }

    /**
     * ### Checks Whether the Cookie Is HTTP-Only
     *
     * Determines whether the HttpOnly attribute is enabled.
     *
     * <code>
     * $cookie->isHttpOnly();
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the HttpOnly attribute is enabled, otherwise false.
     */
    public function isHttpOnly ():bool {

        return $this->http_only;

    }

    /**
     * ### Gets the SameSite Policy
     *
     * Returns the explicitly configured SameSite policy.
     *
     * A null value indicates that the SameSite attribute is omitted from the serialized cookie.
     *
     * <code>
     * $sameSite = $cookie->sameSite();
     *
     * $sameSite?->value;
     * // Lax or null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cookie\Response\SameSite The SameSite policy, or null if unspecified.
     */
    public function sameSite ():?SameSite {

        return $this->same_site;

    }

    /**
     * ### Checks Whether the Cookie Is Partitioned
     *
     * Determines whether the Partitioned attribute is enabled.
     *
     * <code>
     * $cookie->isPartitioned();
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the Partitioned attribute is enabled, otherwise false.
     */
    public function isPartitioned ():bool {

        return $this->partitioned;

    }

    /**
     * ### Gets the Serialized Set-Cookie Header Value
     *
     * Serializes the base cookie and its configured response attributes into a single Set-Cookie header value.
     *
     * Attributes that are not configured are omitted. Boolean attributes are included only when enabled.
     *
     * <code>
     * $cookie = new Cookie(
     *     cookie: $baseCookie,
     *     path: new Path('/'),
     *     max_age: new MaxAge(3600),
     *     secure: true,
     *     http_only: true,
     *     same_site: SameSite::LAX
     * );
     *
     * $cookie->value();
     * // session=abc; Path=/; Max-Age=3600; Secure; HttpOnly; SameSite=Lax
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie::value() To serialize the base cookie.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\Domain::headerValue() To serialize the Domain attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\Path::headerValue() To serialize the Path attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\Expires::headerValue() To serialize the Expires attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\MaxAge::headerValue() To serialize the Max-Age attribute.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\SameSite::headerValue() To serialize the SameSite attribute.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the cookie attributes.
     *
     * @return non-empty-string Serialized Set-Cookie header value.
     */
    public function value ():string {

        $attributes = [$this->cookie->value()];

        if ($this->domain !== null)
            $attributes[] = $this->domain->headerValue();

        if ($this->path !== null)
            $attributes[] = $this->path->headerValue();

        if ($this->expires !== null)
            $attributes[] = $this->expires->headerValue();

        if ($this->max_age !== null)
            $attributes[] = $this->max_age->headerValue();

        if ($this->secure)
            $attributes[] = 'Secure';

        if ($this->http_only)
            $attributes[] = 'HttpOnly';

        if ($this->same_site !== null)
            $attributes[] = $this->same_site->headerValue();

        if ($this->partitioned)
            $attributes[] = 'Partitioned';

        /** @var non-empty-string */
        return Runtime\Str\SB\Delimiter::implode($attributes, '; ');

    }

    /**
     * ### Validates the Cookie Attributes
     *
     * Ensures that security-sensitive cookie attributes and reserved cookie name prefixes satisfy their
     * required constraints.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie::name() To retrieve the cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Name::value() To retrieve the raw cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Response\Path::isRoot() To verify the root path requirement.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check for reserved cookie name prefixes.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie attributes violate
     * security requirements or reserved cookie name prefix restrictions.
     *
     * @return void
     */
    private function validate ():void {

        if ($this->same_site === SameSite::NONE && !$this->secure)
            throw new InvalidFieldValueException(
                'SameSite=None requires the Secure attribute.'
            );

        if ($this->partitioned && !$this->secure)
            throw new InvalidFieldValueException(
                'Partitioned cookies require the Secure attribute.'
            );

        $name = $this->cookie->name()->value();

        if (Runtime\Str\SB\Search::startsWith('__Secure-', $name) && !$this->secure)
            throw new InvalidFieldValueException(
                'Cookies with the __Secure- prefix require Secure.'
            );

        if (Runtime\Str\SB\Search::startsWith('__Host-Http-', $name)) {

            if (
                !$this->secure
                || !$this->http_only
                || $this->domain !== null
                || $this->path?->isRoot() !== true
            ) throw new InvalidFieldValueException(
                'Cookies with the __Host-Http- prefix require Secure, HttpOnly, Path=/, and no Domain.'
            );

        } else if (Runtime\Str\SB\Search::startsWith('__Host-', $name)) {

            if (
                !$this->secure
                || $this->domain !== null
                || $this->path?->isRoot() !== true
            ) throw new InvalidFieldValueException(
                'Cookies with the __Host- prefix require Secure, Path=/, and no Domain.'
            );

        } else if (Runtime\Str\SB\Search::startsWith('__Http-', $name))
            if (!$this->secure || !$this->http_only)
                throw new InvalidFieldValueException(
                    'Cookies with the __Http- prefix require Secure and HttpOnly.'
                );

    }

}