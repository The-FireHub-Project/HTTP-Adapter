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

namespace FireHub\Adapter\HTTP\Request\Target;

use FireHub\Adapter\HTTP\Request\ {
    Method, Target
};
use FireHub\Adapter\HTTP\Request\Method\Standard;
use FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetCombinationException;

/**
 * ### Validates HTTP request-target combinations
 *
 * Ensures that an HTTP request method and request-target form are compatible with the specified HTTP protocol version.
 *
 * Validation is limited to request-target compatibility and does not validate HTTP headers, message bodies, or
 * transport-specific framing.
 * @since 1.0.0
 */
final readonly class Validator {

    /**
     * ### Validates an HTTP request-target combination for HTTP/1.1
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To get the HTTP request method.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Target $target <p>
     * HTTP request target.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetCombinationException If the combination is
     * invalid.
     *
     * @return void
     */
    public static function http11 (Method $method, Target $target):void {

        if ($method === Standard::CONNECT) {

            if (!$target instanceof Authority)
                throw new InvalidTargetCombinationException(
                    'CONNECT requests require an authority-form target.'
                );

            return;

        }

        if ($target instanceof Authority)
            throw new InvalidTargetCombinationException(
                'Authority-form targets require the CONNECT method.'
            );

        if ($target instanceof Asterisk) {

            if ($method !== Standard::OPTIONS)
                throw new InvalidTargetCombinationException(
                    'Asterisk-form targets require the OPTIONS method.'
                );

            return;

        }

        if ($target instanceof Origin || $target instanceof Absolute)
            return;

        throw new InvalidTargetCombinationException(
            'Unsupported HTTP request-target form.'
        );

    }

}