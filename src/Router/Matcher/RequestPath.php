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

namespace FireHub\Adapter\HTTP\Router\Matcher;

use FireHub\Adapter\HTTP\Router\Path\Normalizer;
use FireHub\Runtime;

/**
 * ### Represents a prepared HTTP request path
 *
 * Validates and splits an incoming URI path into raw and canonical segments.
 *
 * Path preparation is performed once per routing operation.
 *
 * Raw segments preserve their original percent-encoded representation for route parameter extraction.
 *
 * Canonical segments are used exclusively for static route matching.
 * @since 1.0.0
 */
final readonly class RequestPath {

    /**
     * ### Raw path segments
     * @since 1.0.0
     *
     * @var list<string>
     */
    private array $segments;

    /**
     * ### Canonical path segments
     * @since 1.0.0
     *
     * @var list<string>
     */
    private array $canonical_segments;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param list<string> $segments <p>
     * Raw path segments.
     * </p>
     * @param list<string> $canonical_segments <p>
     * Canonical path segments.
     * </p>
     *
     * @return void
     */
    private function __construct (array $segments, array $canonical_segments) {

        $this->segments = $segments;
        $this->canonical_segments = $canonical_segments;

    }

    /**
     * ### Parses an incoming request path
     *
     * Validates the URI path and prepares its segments for route matching.
     *
     * Returns null when the path is invalid or unsupported by the router.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Matcher\RequestPath;
     *
     * $path = RequestPath::parse('/users/123');
     *
     * $path?->segments();
     * // ['users', '123']
     *
     * RequestPath::parse('/users/');
     * // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Path\Normalizer::segment() To normalize URI segments.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the path format.
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the path ends with a slash.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the path into segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the path from the URI.
     *
     * @param string $path <p>
     * Incoming URI path.
     * </p>
     *
     * @return null|self Prepared path, or null if invalid.
     */
    public static function parse (string $path):?self {

        if ($path === '/')
            return new self([], []);

        if (
            $path === ''
            || $path[0] !== '/'
            || Runtime\Str\SB\Search::endsWith('/', $path)
        ) return null;

        if (!Runtime\Str\SB\Regex::match(
            '/\A(?:\/|[A-Za-z0-9._~!$&\'()*+,;=:@-]|%[A-Fa-f0-9]{2})+\z/',
            $path
        )) return null;

        $segments = Runtime\Str\SB\Delimiter::explode(
            Runtime\Str\SB\Access::part($path, 1),
            '/'
        );

        /** @var list<string> $canonical_segments */
        $canonical_segments = [];

        foreach ($segments as $segment) {

            if ($segment === '') return null;

            $canonical_segments[] = Normalizer::segment($segment);

        }

        return new self($segments, $canonical_segments);

    }

    /**
     * ### Gets raw URI path segments
     *
     * @since 1.0.0
     *
     * @return list<string> Raw path segments.
     */
    public function segments ():array {

        return $this->segments;

    }

    /**
     * ### Gets canonical URI path segments
     *
     * @since 1.0.0
     *
     * @return list<string> Canonical path segments.
     */
    public function canonicalSegments ():array {

        return $this->canonical_segments;

    }

}