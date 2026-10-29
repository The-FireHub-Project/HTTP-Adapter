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

/**
 * ### Represents an HTTP response cache directive
 *
 * Encapsulates a validated Cache-Control response directive.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class ResponseDirective extends ValueObject implements Directive {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param non-empty-string $serialized <p>
     * Serialized directive.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private string $serialized
    ) {}

    /**
     * ### Creates a maximum-age directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize the maximum age.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return self Updated response directive.
     */
    public static function maxAge (int $seconds):self {

        return new self(
            Serializer::seconds(Response::MAX_AGE->value, $seconds)
        );

    }

    /**
     * ### Creates a no-cache directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::fields() To serialize the no-cache directive.
     *
     * @param non-empty-string ...$fields <p>
     * Optional response field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the field name is invalid or
     * the list is empty.
     *
     * @return self Updated response directive.
     */
    public static function noCache (string ...$fields):self {

        return new self(
            $fields === []
                ? Response::NO_CACHE->value
                : Serializer::fields(Response::NO_CACHE->value, $fields) // @phpstan-ignore argument.type
        );

    }

    /**
     * ### Creates a no-store directive
     * @since 1.0.0
     *
     * @return self Updated response directive.
     */
    public static function noStore ():self {

        return new self(Response::NO_STORE->value);

    }

    /**
     * ### Creates a no-transform directive
     * @since 1.0.0
     *
     * @return self Updated response directive.
     */
    public static function noTransform ():self {

        return new self(Response::NO_TRANSFORM->value);

    }

    /**
     * ### Creates a must-revalidate directive
     * @since 1.0.0
     *
     * @return self Updated response directive.
     */
    public static function mustRevalidate ():self {

        return new self(Response::MUST_REVALIDATE->value);

    }

    /**
     * ### Creates a proxy-revalidate directive
     * @since 1.0.0
     *
     * @return self Updated response directive.
     */
    public static function proxyRevalidate ():self {

        return new self(Response::PROXY_REVALIDATE->value);

    }

    /**
     * ### Creates a public directive
     * @since 1.0.0
     *
     * @return self Updated response directive.
     */
    public static function public ():self {

        return new self(Response::PUBLIC->value);

    }

    /**
     * ### Creates a private directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::fields() To serialize the private directive.
     *
     * @param non-empty-string ...$fields <p>
     * Optional response field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the field name is invalid or
     * the list is empty.
     *
     * @return self Updated response directive.
     */
    public static function private (string ...$fields):self {

        return new self(
            $fields === []
                ? Response::PRIVATE->value
                : Serializer::fields(Response::PRIVATE->value, $fields) // @phpstan-ignore argument.type
        );

    }

    /**
     * ### Creates a shared-cache maximum-age directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize the maximum age.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return self Updated response directive.
     */
    public static function sMaxAge (int $seconds):self {

        return new self(
            Serializer::seconds(Response::S_MAXAGE->value, $seconds)
        );

    }

    /**
     * ### Creates an immutable directive
     * @since 1.0.0
     *
     * @return self Updated response directive.
     */
    public static function immutable ():self {

        return new self(Response::IMMUTABLE->value);

    }

    /**
     * ### Creates a stale-while-revalidate directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize the maximum age.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return self Updated response directive.
     */
    public static function staleWhileRevalidate (int $seconds):self {

        return new self(
            Serializer::seconds(
                Response::STALE_WHILE_REVALIDATE->value,
                $seconds
            )
        );

    }

    /**
     * ### Creates a stale-if-error directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize the maximum age.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return self Updated response directive.
     */
    public static function staleIfError (int $seconds):self {

        return new self(
            Serializer::seconds(Response::STALE_IF_ERROR->value, $seconds)
        );

    }

    /**
     * ### Creates a must-understand directive
     * @since 1.0.0
     *
     * @return self Updated response directive.
     */
    public static function mustUnderstand ():self {

        return new self(Response::MUST_UNDERSTAND->value);

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