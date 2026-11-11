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

namespace FireHub\Adapter\HTTP\Header\CacheControl;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP response Cache-Control directive
 *
 * Stores the directive type, optional delta-seconds parameter, and optional response header field names.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class ResponseDirective extends ValueObject implements Directive {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\CacheControl\Response $type <p>
     * Response directive type.
     * </p>
     * @param null|non-negative-int $seconds <p>
     * Optional delta-seconds parameter.
     * </p>
     * @param list<non-empty-string> $fields <p>
     * Optional response header field names.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private Response $type,
        private ?int $seconds = null,
        private array $fields = []
    ) {}

    /**
     * ### Creates a maximum-age directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective::validateSeconds() To validate seconds.
     *
     * @param int $seconds <p>
     * Maximum response age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return self New response directive.
     */
    public static function maxAge (int $seconds):self {

        self::validateSeconds($seconds);

        return new self(Response::MAX_AGE, $seconds);

    }

    /**
     * ### Creates a no-cache directive
     *
     * Optionally specifies response header fields that require successful validation before reuse.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective::validateFields() To validate field names.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove array keys.
     *
     * @param string ...$fields <p>
     * Optional response header field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a field name is invalid.
     *
     * @return self New response directive.
     */
    public static function noCache (string ...$fields):self {

        $fields = Runtime\Arr\Access::values($fields);

        self::validateFields($fields);

        return new self(
            Response::NO_CACHE,
            null,
            $fields
        );

    }

    /**
     * ### Creates a no-store directive
     * @since 1.0.0
     *
     * @return self New response directive.
     */
    public static function noStore ():self {

        return new self(Response::NO_STORE);

    }

    /**
     * ### Creates a no-transform directive
     * @since 1.0.0
     *
     * @return self New response directive.
     */
    public static function noTransform ():self {

        return new self(Response::NO_TRANSFORM);

    }

    /**
     * ### Creates a must-revalidate directive
     * @since 1.0.0
     *
     * @return self New response directive.
     */
    public static function mustRevalidate ():self {

        return new self(Response::MUST_REVALIDATE);

    }

    /**
     * ### Creates a proxy-revalidate directive
     * @since 1.0.0
     *
     * @return self New response directive.
     */
    public static function proxyRevalidate ():self {

        return new self(Response::PROXY_REVALIDATE);

    }

    /**
     * ### Creates a public directive
     * @since 1.0.0
     *
     * @return self New response directive.
     */
    public static function public ():self {

        return new self(Response::PUBLIC);

    }

    /**
     * ### Creates a private directive
     *
     * Optionally specifies response header fields restricted from storage by shared caches.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective::validateFields() To validate field names.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove array keys.
     *
     * @param string ...$fields <p>
     * Optional response header field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a field name is invalid.
     *
     * @return self New response directive.
     */
    public static function private (string ...$fields):self {

        $fields = Runtime\Arr\Access::values($fields);

        self::validateFields($fields);

        return new self(
            Response::PRIVATE,
            null,
            $fields
        );

    }

    /**
     * ### Creates a shared-cache maximum-age directive
     * @since 1.0.0
     *
     * @param int $seconds <p>
     * Maximum shared-cache age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return self New response directive.
     */
    public static function sMaxAge (int $seconds):self {

        self::validateSeconds($seconds);

        return new self(Response::S_MAXAGE, $seconds);

    }

    /**
     * ### Creates an immutable directive
     * @since 1.0.0
     *
     * @return self New response directive.
     */
    public static function immutable ():self {

        return new self(Response::IMMUTABLE);

    }

    /**
     * ### Creates a stale-while-revalidate directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective::validateSeconds() To validate seconds.
     *
     * @param int $seconds <p>
     * Allowed staleness during revalidation in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return self New response directive.
     */
    public static function staleWhileRevalidate (int $seconds):self {

        self::validateSeconds($seconds);

        return new self(
            Response::STALE_WHILE_REVALIDATE,
            $seconds
        );

    }

    /**
     * ### Creates a stale-if-error directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective::validateSeconds() To validate seconds.
     *
     * @param int $seconds <p>
     * Allowed staleness when an error occurs, in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return self New response directive.
     */
    public static function staleIfError (int $seconds):self {

        self::validateSeconds($seconds);

        return new self(
            Response::STALE_IF_ERROR,
            $seconds
        );

    }

    /**
     * ### Creates a must-understand directive
     * @since 1.0.0
     *
     * @return self New response directive.
     */
    public static function mustUnderstand ():self {

        return new self(Response::MUST_UNDERSTAND);

    }

    /**
     * ### Gets the response directive type
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\CacheControl\Response Response directive type.
     */
    public function type ():Response {

        return $this->type;

    }

    /**
     * ### Gets the directive delta-seconds parameter
     *
     * Returns null when the directive has no delta-seconds parameter.
     * @since 1.0.0
     *
     * @return null|non-negative-int Delta-seconds value or null.
     */
    public function seconds ():?int {

        return $this->seconds;

    }

    /**
     * ### Gets the response header field names
     *
     * Returns an empty list when no field names are specified.
     * @since 1.0.0
     *
     * @return list<non-empty-string> Response header field names.
     */
    public function fields ():array {

        return $this->fields;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the directive fields.
     */
    public function value ():string {

        if ($this->seconds !== null)
            return $this->type->value.'='.$this->seconds;

        if ($this->fields !== [])
            return $this->type->value.'="'.
                Runtime\Str\SB\Delimiter::implode($this->fields, ', ').
                '"';

        return $this->type->value;

    }

    /**
     * ### Validates a delta-seconds parameter
     * @since 1.0.0
     *
     * @param int $seconds <p>
     * Delta-seconds value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return void
     *
     * @phpstan-assert non-negative-int $seconds
     */
    private static function validateSeconds (int $seconds):void {

        if ($seconds < 0)
            throw new InvalidCacheDirectiveException(
                'Cache-Control seconds must not be negative.'
            );

    }

    /**
     * ### Validates response header field names
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP field names.
     *
     * @param list<string> $fields <p>
     * Response header field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a field name is invalid.
     *
     * @return void
     *
     * @phpstan-assert list<non-empty-string> $fields
     */
    private static function validateFields (array $fields):void {

        foreach ($fields as $field) {

            if (!Runtime\Str\SB\Regex::match(
                '/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/',
                $field
            )) {

                throw new InvalidCacheDirectiveException(
                    'Invalid Cache-Control field name.'
                );

            }

        }

    }

}