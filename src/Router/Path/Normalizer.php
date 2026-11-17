<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Router\Path;

use FireHub\Runtime;

/**
 * ### Normalizes URI path segments for HTTP routing
 *
 * Applies consistent percent-encoding normalization to URI path segments.
 *
 * Percent-encoded unreserved ASCII characters are decoded, while all remaining percent-encoded octets are preserved
 * with uppercase hexadecimal digits.
 *
 * Reserved characters are never decoded during normalization.
 *
 * Normalization does not change path segment boundaries.
 * @since 1.0.0
 */
final class Normalizer {

    /**
     * ### Normalizes a URI path segment
     *
     * Decodes percent-encoded unreserved characters and converts all remaining percent-encoded hexadecimal digits to
     * uppercase.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Normalizer;
     *
     * Normalizer::segment('%41');
     * // 'A'
     *
     * Normalizer::segment('%7e');
     * // '~'
     *
     * Normalizer::segment('a%2fb');
     * // 'a%2Fb'
     *
     * Normalizer::segment('%20');
     * // '%20'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::replaceUsing() To normalize percent-encoded octets.
     * @uses \FireHub\Runtime\Encoding::hexDecode() To decode hexadecimal octets.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To identify unreserved characters.
     * @uses \FireHub\Runtime\Str\SB\Casing::toUpper() To normalize hexadecimal digits.
     * @uses \FireHub\Runtime\DataIs::string() To check if the match is a string.
     *
     * @param string $segment <p>
     * URI path segment containing valid percent-encoding.
     * </p>
     *
     * @return string Normalized URI path segment.
     */
    public static function segment (string $segment):string {

        return Runtime\Str\SB\Regex::replaceUsing(
            '/%([A-Fa-f0-9]{2})/',
            static function (array $matches):string {

                $hex = $matches[1] ?? null;

                if (!Runtime\DataIs::string($hex))
                    return $matches[0] ?? '';

                $character = Runtime\Encoding::hexDecode($hex);

                if ($character !== false && Runtime\Str\SB\Regex::match(
                        '/\A[A-Za-z0-9._~-]\z/',
                        $character
                    )) return $character;

                return '%' . Runtime\Str\SB\Casing::toUpper($hex);

            },
            $segment
        );

    }

}