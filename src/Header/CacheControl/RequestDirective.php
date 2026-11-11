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
 * ### Represents an HTTP request cache directive
 *
 * Encapsulates structured Cache-Control request directives, including their type
 * and optional delta-seconds or response field-name parameters.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class RequestDirective extends ValueObject implements Directive {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\CacheControl\Request $type <p>
     * Cache directive type.
     * </p>
     * @param non-empty-string $serialized <p>
     * Validated serialized directive.
     * </p>
     * @param null|int<0, max> $seconds <p>
     * Optional delta-seconds value.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private Request $type,
        private string $serialized,
        private ?int $seconds = null
    ) {}

    /**
     * ### Creates the max-age request directive
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
     * @return self New request cache directive.
     */
    public static function maxAge (int $seconds):self {

        return new self(
            Request::MAX_AGE,
            Serializer::seconds(Request::MAX_AGE->value, $seconds),
            $seconds
        );

    }

    /**
     * ### Creates the max-stale request directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize a specified maximum staleness.
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize an unspecified maximum staleness.
     *
     * @param null|int<0, max> $seconds <p>
     * Optional maximum staleness in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If delta-seconds is negative.
     *
     * @return self New request cache directive.
     */
    public static function maxStale (?int $seconds = null):self {

        return new self(
            Request::MAX_STALE,
            $seconds === null
                ? Serializer::flag(Request::MAX_STALE->value)
                : Serializer::seconds(Request::MAX_STALE->value, $seconds),
            $seconds
        );

    }

    /**
     * ### Creates the min-fresh request directive
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
     * @return self New request cache directive.
     */
    public static function minFresh (int $seconds):self {

        return new self(
            Request::MIN_FRESH,
            Serializer::seconds(Request::MIN_FRESH->value, $seconds),
            $seconds
        );

    }

    /**
     * ### Creates the no-cache request directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New request cache directive.
     */
    public static function noCache ():self {

        return new self(
            Request::NO_CACHE,
            Serializer::flag(Request::NO_CACHE->value)
        );

    }

    /**
     * ### Creates the no-store request directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New request cache directive.
     */
    public static function noStore ():self {

        return new self(
            Request::NO_STORE,
            Serializer::flag(Request::NO_STORE->value)
        );

    }

    /**
     * ### Creates the no-transform request directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New request cache directive.
     */
    public static function noTransform ():self {

        return new self(
            Request::NO_TRANSFORM,
            Serializer::flag(Request::NO_TRANSFORM->value)
        );

    }

    /**
     * ### Creates the only-if-cached request directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the directive.
     *
     * @return self New request cache directive.
     */
    public static function onlyIfCached ():self {

        return new self(
            Request::ONLY_IF_CACHED,
            Serializer::flag(Request::ONLY_IF_CACHED->value)
        );

    }

    /**
     * ### Gets the cache directive type
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\CacheControl\Request Cache directive type.
     */
    public function type ():Request {

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
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->serialized;

    }

}
