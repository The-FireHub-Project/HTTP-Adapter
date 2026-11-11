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
 * ### Represents an HTTP request Cache-Control directive
 *
 * Stores the directive type and its optional delta-seconds parameter without retaining a serialized HTTP
 * representation.
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
     * Request directive type.
     * </p>
     * @param null|non-negative-int $seconds <p>
     * Optional delta-seconds parameter.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private Request $type,
        private ?int $seconds = null
    ) {}

    /**
     * ### Creates a maximum-age directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective::value() To get the directive value.
     *
     * @param int $seconds <p>
     * Maximum acceptable response age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return self New request directive.
     */
    public static function maxAge (int $seconds):self {

        self::validateSeconds($seconds);

        return new self(Request::MAX_AGE, $seconds);

    }

    /**
     * ### Creates a maximum-stale directive
     *
     * An omitted delta-seconds parameter allows stale responses without specifying a maximum staleness limit.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective::value() To get the directive value.
     *
     * @param null|int $seconds <p>
     * Optional maximum staleness in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return self New request directive.
     */
    public static function maxStale (?int $seconds = null):self {

        if ($seconds !== null)
            self::validateSeconds($seconds);

        return new self(Request::MAX_STALE, $seconds);

    }

    /**
     * ### Creates a minimum-fresh directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective::value() To get the directive value.
     *
     * @param int $seconds <p>
     * Minimum remaining freshness in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If seconds are negative.
     *
     * @return self New request directive.
     */
    public static function minFresh (int $seconds):self {

        self::validateSeconds($seconds);

        return new self(Request::MIN_FRESH, $seconds);

    }

    /**
     * ### Creates a no-cache directive
     * @since 1.0.0
     *
     * @return self New request directive.
     */
    public static function noCache ():self {

        return new self(Request::NO_CACHE);

    }

    /**
     * ### Creates a no-store directive
     * @since 1.0.0
     *
     * @return self New request directive.
     */
    public static function noStore ():self {

        return new self(Request::NO_STORE);

    }

    /**
     * ### Creates a no-transform directive
     * @since 1.0.0
     *
     * @return self New request directive.
     */
    public static function noTransform ():self {

        return new self(Request::NO_TRANSFORM);

    }

    /**
     * ### Creates an only-if-cached directive
     * @since 1.0.0
     *
     * @return self New request directive.
     */
    public static function onlyIfCached ():self {

        return new self(Request::ONLY_IF_CACHED);

    }

    /**
     * ### Gets the request directive type
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\CacheControl\Request Request directive type.
     */
    public function type ():Request {

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
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->seconds === null
            ? $this->type->value
            : $this->type->value.'='.$this->seconds;

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

}