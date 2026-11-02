<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Cookie;

use FireHub\Adapter\HTTP\Cookie;
use FireHub\Foundation\Temporal\DateTime;
use FireHub\Adapter\HTTP\Cookie\Exception\ {
    InvalidCookiePartitionedException, InvalidCookiePrefixException, InvalidCookieSameSiteException
};
use FireHub\Runtime;

/**
 * ### Represents an HTTP response cookie
 *
 * Immutable representation of a cookie transmitted through the Set-Cookie response header.
 *
 * Supports Domain, Path, Expires, Max-Age, Secure, HttpOnly, and SameSite attributes.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Adapter\HTTP\Cookie<TValue>
 */
final readonly class Response extends Cookie {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the cookie name and value.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the cookie name and value.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the cookie name and value.
     * @uses \FireHub\Adapter\HTTP\Cookie\Path::value() To validate the cookie path.
     *
     * @param non-empty-string $name <p>
     * Cookie name.
     * </p>
     * @param string $value <p>
     * Cookie value.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Cookie\Domain $domain <p>
     * Cookie domain.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Cookie\Path $path <p>
     * Cookie path.
     * </p>
     * @param null|\FireHub\Foundation\Temporal\DateTime<non-empty-string> $expires <p>
     * Cookie expiration date.
     * </p>
     * @param null|int $max_age <p>
     * Cookie maximum age in seconds.
     * </p>
     * @param bool $secure <p>
     * Whether the cookie requires a secure connection.
     * </p>
     * @param bool $http_only <p>
     * Whether JavaScript access is restricted.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Cookie\SameSite $same_site <p>
     * SameSite policy.
     * </p>
     * @param bool $partitioned <p>
     * Whether the cookie is partitioned.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        string $name,
        string $value,
        private ?Domain $domain = null,
        private ?Path $path = null,
        private ?DateTime $expires = null,
        private ?int $max_age = null,
        private bool $secure = true,
        private bool $http_only = true,
        private ?SameSite $same_site = SameSite::LAX,
        private bool $partitioned = false
    ) {

        parent::__construct($name, $value);

        $this->guard(
            fn() => $this->same_site !== SameSite::NONE || $this->secure,
            fn() => new InvalidCookieSameSiteException
        );

        $this->guard(
            fn() => !$this->partitioned || $this->secure,
            fn() => new InvalidCookiePartitionedException
        );

        $this->guard(
            fn() => !Runtime\Str\SB\Search::startsWith(
                '__Secure-',
                    $this->name
                ) || $this->secure,
            fn() => new InvalidCookiePrefixException
        );

        $this->guard(
            fn() => !Runtime\Str\SB\Search::startsWith(
                '__Host-',
                    $this->name
                ) || (
                    $this->secure
                    && $this->domain === null
                    && $this->path?->value() === '/'
                ),
            fn() => new InvalidCookiePrefixException
        );

        $this->guard(
            fn() => !Runtime\Str\SB\Search::startsWith(
                    '__Http-',
                    $this->name
                ) || (
                    $this->secure
                    && $this->http_only
                ),
            fn() => new InvalidCookiePrefixException
        );

        $this->guard(
            fn() => !Runtime\Str\SB\Search::startsWith(
                    '__Host-Http-',
                    $this->name
                ) || (
                    $this->secure
                    && $this->http_only
                    && $this->domain === null
                    && $this->path?->value() === '/'
                ),
            fn() => new InvalidCookiePrefixException
        );

    }

    /**
     * ### Returns a cookie with a different value
     * @since 1.0.0
     *
     * @template TNewValue of TValue
     *
     * @param TNewValue $value <p>
     * New cookie value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TNewValue> New cookie instance.
     */
    public function withValue (string $value):self {

        /** @var self<TNewValue> */
        return new self(
            $this->name,
            $value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            $this->secure,
            $this->http_only,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie with a different domain
     * @since 1.0.0
     *
     * @param null|\FireHub\Adapter\HTTP\Cookie\Domain $domain <p>
     * New cookie domain.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withDomain (?Domain $domain):self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $domain,
            $this->path,
            $this->expires,
            $this->max_age,
            $this->secure,
            $this->http_only,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie without a Domain attribute
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutDomain ():self {

        return $this->withDomain(null);

    }

    /**
     * ### Returns a cookie with a different path
     * @since 1.0.0
     *
     * @param null|\FireHub\Adapter\HTTP\Cookie\Path $path <p>
     * New cookie path.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withPath (?Path $path):self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $path,
            $this->expires,
            $this->max_age,
            $this->secure,
            $this->http_only,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie without a Path attribute
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutPath ():self {

        return $this->withPath(null);

    }

    /**
     * ### Returns a cookie with a different expiration date
     * @since 1.0.0
     *
     * @param null|\FireHub\Foundation\Temporal\DateTime<non-empty-string> $expires <p>
     * New expiration date.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withExpires (?DateTime $expires):self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $expires,
            $this->max_age,
            $this->secure,
            $this->http_only,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie without an Expires attribute
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutExpires ():self {

        return $this->withExpires(null);

    }

    /**
     * ### Returns a cookie with a different maximum age
     * @since 1.0.0
     *
     * @param null|int $max_age <p>
     * Maximum age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withMaxAge (?int $max_age):self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $max_age,
            $this->secure,
            $this->http_only,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie without a Max-Age attribute
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutMaxAge ():self {

        return $this->withMaxAge(null);

    }

    /**
     * ### Returns a cookie with Secure setting
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withSecure ():self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            true,
            $this->http_only,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie without Secure setting
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutSecure ():self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            false,
            $this->http_only,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie with HttpOnly setting
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withHttpOnly ():self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            $this->secure,
            true,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie without HttpOnly setting
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutHttpOnly ():self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            $this->secure,
            false,
            $this->same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie with a different SameSite policy
     * @since 1.0.0
     *
     * @param null|\FireHub\Adapter\HTTP\Cookie\SameSite $same_site <p>
     * SameSite policy.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withSameSite (?SameSite $same_site):self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            $this->secure,
            $this->http_only,
            $same_site,
            $this->partitioned
        );

    }

    /**
     * ### Returns a cookie without a SameSite attribute
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutSameSite ():self {

        return $this->withSameSite(null);

    }

    /**
     * ### Returns a cookie with partitioned setting
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withPartitioned ():self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            $this->secure,
            $this->http_only,
            $this->same_site,
            true
        );

    }

    /**
     * ### Returns a cookie without partitioned setting
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<TValue> New cookie instance.
     */
    public function withoutPartitioned ():self {

        /** @var self<TValue> */
        return new self(
            $this->name,
            $this->value,
            $this->domain,
            $this->path,
            $this->expires,
            $this->max_age,
            $this->secure,
            $this->http_only,
            $this->same_site,
            false
        );

    }

    /**
     * ### Gets the cookie domain
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Cookie\Domain Cookie domain.
     */
    public function domain ():?Domain {

        return $this->domain;

    }

    /**
     * ### Gets the cookie path
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Cookie\Path Cookie path.
     */
    public function path ():?Path {

        return $this->path;

    }

    /**
     * ### Gets the cookie expiration date
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Temporal\DateTime<non-empty-string> Expiration date.
     */
    public function expires ():?DateTime {

        return $this->expires;

    }

    /**
     * ### Gets the cookie maximum age
     * @since 1.0.0
     *
     * @return null|int Maximum age in seconds.
     */
    public function maxAge ():?int {

        return $this->max_age;

    }

    /**
     * ### Checks whether the cookie is secure
     * @since 1.0.0
     *
     * @return bool True if Secure is enabled.
     */
    public function secure ():bool {

        return $this->secure;

    }

    /**
     * ### Checks whether the cookie is HTTP-only
     * @since 1.0.0
     *
     * @return bool True if HttpOnly is enabled.
     */
    public function httpOnly ():bool {

        return $this->http_only;

    }

    /**
     * ### Gets the SameSite policy
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Cookie\SameSite|null SameSite policy.
     */
    public function sameSite ():?SameSite {

        return $this->same_site;

    }

    /**
     * ### Checks whether the cookie is partitioned
     * @since 1.0.0
     *
     * @return bool True if the cookie is partitioned.
     */
    public function partitioned ():bool {

        return $this->partitioned;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     */
    protected function comparisonValue ():array {

        return [
            $this->name,
            $this->value,
            $this->domain?->value(),
            $this->path?->value(),
            $this->expires?->timestamp(),
            $this->max_age,
            $this->secure,
            $this->http_only,
            $this->same_site?->value,
            $this->partitioned
        ];

    }

}