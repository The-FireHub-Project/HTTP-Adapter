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

/**
 * ### Represents an HTTP response cache directive
 *
 * Encapsulates structured Cache-Control response directives, including their type
 * and optional delta-seconds or response field-name parameters.
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
     * Cache directive type.
     * </p>
     * @param non-empty-string $serialized <p>
     * Validated serialized directive.
     * </p>
     * @param null|int<0, max> $seconds <p>
     * Optional delta-seconds value.
     * </p>
     * @param list<non-empty-string> $fields <p>
     * Optional response field names.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private Response $type,
        private string $serialized,
        private ?int $seconds = null,
        private array $fields = []
    ) {}

    /**
     * ### Creates the max-age response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize delta-seconds.
     *
     * @param int<0, max> $seconds <p>
     * Delta-seconds value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If delta-seconds is negative.
     *
     * @return self New response cache directive.
     */
    public static function maxAge (int $seconds):self {

        return new self(
            Response::MAX_AGE,
            Serializer::seconds(Response::MAX_AGE->value, $seconds),
            $seconds
        );

    }

    /**
     * ### Creates the no-cache response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::fields() To serialize field names when present.
     *
     * @param non-empty-string ...$fields <p>
     * Optional response header field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a field name is invalid.
     *
     * @return self New response cache directive.
     */
    public static function noCache (string ...$fields):self {

        foreach ($fields as $field) {

            if ($field === '')
                throw new InvalidCacheDirectiveException(
                    'Cache-Control field name cannot be empty.'
                );

        }

        return new self(
            Response::NO_CACHE,
            $fields === []
                ? Serializer::flag(Response::NO_CACHE->value)
                : Serializer::fields(Response::NO_CACHE->value, $fields),
            null,
            $fields
        );

    }

    /**
     * ### Creates the no-store response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New response cache directive.
     */
    public static function noStore ():self {

        return new self(
            Response::NO_STORE,
            Serializer::flag(Response::NO_STORE->value)
        );

    }

    /**
     * ### Creates the no-transform response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New response cache directive.
     */
    public static function noTransform ():self {

        return new self(
            Response::NO_TRANSFORM,
            Serializer::flag(Response::NO_TRANSFORM->value)
        );

    }

    /**
     * ### Creates the must-revalidate response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New response cache directive.
     */
    public static function mustRevalidate ():self {

        return new self(
            Response::MUST_REVALIDATE,
            Serializer::flag(Response::MUST_REVALIDATE->value)
        );

    }

    /**
     * ### Creates the proxy-revalidate response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New response cache directive.
     */
    public static function proxyRevalidate ():self {

        return new self(
            Response::PROXY_REVALIDATE,
            Serializer::flag(Response::PROXY_REVALIDATE->value)
        );

    }

    /**
     * ### Creates the public response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New response cache directive.
     */
    public static function public ():self {

        return new self(
            Response::PUBLIC,
            Serializer::flag(Response::PUBLIC->value)
        );

    }

    /**
     * ### Creates the private response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::fields() To serialize field names when present.
     *
     * @param non-empty-string ...$fields <p>
     * Optional response header field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a field name is invalid.
     *
     * @return self New response cache directive.
     */
    public static function private (string ...$fields):self {

        foreach ($fields as $field) {

            if ($field === '')
                throw new InvalidCacheDirectiveException(
                    'Cache-Control field name cannot be empty.'
                );

        }

        return new self(
            Response::PRIVATE,
            $fields === []
                ? Serializer::flag(Response::PRIVATE->value)
                : Serializer::fields(Response::PRIVATE->value, $fields),
            null,
            $fields
        );

    }

    /**
     * ### Creates the s-maxage response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize delta-seconds.
     *
     * @param int<0, max> $seconds <p>
     * Delta-seconds value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If delta-seconds is negative.
     *
     * @return self New response cache directive.
     */
    public static function sMaxAge (int $seconds):self {

        return new self(
            Response::S_MAXAGE,
            Serializer::seconds(Response::S_MAXAGE->value, $seconds),
            $seconds
        );

    }

    /**
     * ### Creates the immutable response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New response cache directive.
     */
    public static function immutable ():self {

        return new self(
            Response::IMMUTABLE,
            Serializer::flag(Response::IMMUTABLE->value)
        );

    }

    /**
     * ### Creates the stale-while-revalidate response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize delta-seconds.
     *
     * @param int<0, max> $seconds <p>
     * Delta-seconds value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If delta-seconds is negative.
     *
     * @return self New response cache directive.
     */
    public static function staleWhileRevalidate (int $seconds):self {

        return new self(
            Response::STALE_WHILE_REVALIDATE,
            Serializer::seconds(Response::STALE_WHILE_REVALIDATE->value, $seconds),
            $seconds
        );

    }

    /**
     * ### Creates the stale-if-error response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize delta-seconds.
     *
     * @param int<0, max> $seconds <p>
     * Delta-seconds value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If delta-seconds is negative.
     *
     * @return self New response cache directive.
     */
    public static function staleIfError (int $seconds):self {

        return new self(
            Response::STALE_IF_ERROR,
            Serializer::seconds(Response::STALE_IF_ERROR->value, $seconds),
            $seconds
        );

    }

    /**
     * ### Creates the must-understand response directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New response cache directive.
     */
    public static function mustUnderstand ():self {

        return new self(
            Response::MUST_UNDERSTAND,
            Serializer::flag(Response::MUST_UNDERSTAND->value)
        );

    }

    /**
     * ### Gets the cache directive type
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\CacheControl\Response Cache directive type.
     */
    public function type ():Response {

        return $this->type;

    }

    /**
     * ### Gets the directive delta-seconds value
     *
     * Returns null if the directive has no specified delta-seconds value.
     * @since 1.0.0
     *
     * @return null|int<0, max> Delta-seconds value, or null if absent.
     */
    public function seconds ():?int {

        return $this->seconds;

    }

    /**
     * ### Gets the response header field names
     *
     * Returns an empty list if no field-name parameters were specified.
     * @since 1.0.0
     *
     * @return list<non-empty-string> Response field names.
     */
    public function fields ():array {

        return $this->fields;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->serialized;

    }

}
