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
 * ### Represents an HTTP request cache directive
 *
 * Encapsulates a validated Cache-Control request directive.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class RequestDirective extends ValueObject implements Directive {

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
     * @param positive-int $seconds <p>
     * Maximum age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return self Updated request directive.
     */
    public static function maxAge (int $seconds):self {

        return new self(
            Serializer::seconds(Request::MAX_AGE->value, $seconds)
        );

    }

    /**
     * ### Creates a maximum-stale directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::flag() To serialize the maximum stale directive.
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize the maximum stale directive.
     *
     * @param null|positive-int $seconds <p>
     * Maximum stale age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return self Updated request directive.
     */
    public static function maxStale (?int $seconds = null):self {

        return new self(
            $seconds === null
                ? Serializer::flag(Request::MAX_STALE->value)
                : Serializer::seconds(Request::MAX_STALE->value, $seconds)
        );

    }

    /**
     * ### Creates a minimum-fresh directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\Serializer::seconds() To serialize the minimum fresh age.
     *
     * @param positive-int $seconds <p>
     * Minimum fresh age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return self Updated request directive.
     */
    public static function minFresh (int $seconds):self {

        return new self(
            Serializer::seconds(Request::MIN_FRESH->value, $seconds)
        );

    }

    /**
     * ### Creates a no-cache directive
     * @since 1.0.0
     *
     * @return self Updated request directive.
     */
    public static function noCache ():self {

        return new self(Request::NO_CACHE->value);

    }

    /**
     * ### Creates a no-store directive
     * @since 1.0.0
     *
     * @return self Updated request directive.
     */
    public static function noStore ():self {

        return new self(Request::NO_STORE->value);

    }

    /**
     * ### Creates a no-transform directive
     * @since 1.0.0
     *
     * @return self Updated request directive.
     */
    public static function noTransform ():self {

        return new self(Request::NO_TRANSFORM->value);

    }

    /**
     * ### Creates an only-if-cached directive
     * @since 1.0.0
     *
     * @return self Updated request directive.
     */
    public static function onlyIfCached ():self {

        return new self(Request::ONLY_IF_CACHED->value);

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