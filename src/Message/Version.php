<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Message;

use FireHub\Runtime;

/**
 * ### Defines supported HTTP protocol versions
 *
 * Represents HTTP protocol versions used by HTTP requests and responses.
 * @since 1.0.0
 */
enum Version:string {

    /**
     * ### HTTP/1.0
     * @since 1.0.0
     */
    case HTTP_1_0 = '1.0';

    /**
     * ### HTTP/1.1
     * @since 1.0.0
     */
    case HTTP_1_1 = '1.1';

    /**
     * ### HTTP/2
     * @since 1.0.0
     */
    case HTTP_2 = '2';

    /**
     * ### HTTP/3
     * @since 1.0.0
     */
    case HTTP_3 = '3';

    /**
     * ### Parses an HTTP protocol version
     *
     * Accepts canonical version values, equivalent dotted representations,
     * and versions prefixed with "HTTP/".
     *
     * Returns null when the version is unsupported.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Message\Version;
     *
     * // Version::HTTP_1_1
     * $version = Version::parse('HTTP/1.1');
     *
     * // Version::HTTP_2
     * $version = Version::parse('2.0');
     *
     * // Version::HTTP_3
     * $version = Version::parse('HTTP/3');
     *
     * // null
     * $version = Version::parse('HTTP/4');
     * </code>
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the version is prefixed with "HTTP/".
     * @uses \FireHub\Runtime\Str\SB\Access::part() To remove the "HTTP/" prefix from the version.
     *
     * @param string $version <p>
     * HTTP protocol version without the HTTP/ prefix.
     * </p>
     *
     * @return null|self Parsed HTTP version, or null if unsupported.
     */
    public static function parse (string $version):?self {

        if (Runtime\Str\SB\Search::startsWith('HTTP/', $version))
            $version = Runtime\Str\SB\Access::part($version, 5);

        return self::tryFrom(
            match ($version) {
                '2.0' => self::HTTP_2->value,
                '3.0' => self::HTTP_3->value,
                default => $version
            }
        );

    }

}