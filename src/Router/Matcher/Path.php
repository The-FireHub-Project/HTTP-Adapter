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

namespace FireHub\Adapter\HTTP\Router\Matcher;

use FireHub\Adapter\HTTP\Router\Path\ {
    Normalizer, Pattern
};
use FireHub\Runtime;

/**
 * ### Matches HTTP request paths against route patterns
 *
 * Compares URI paths with registered route patterns and extracts named parameters from matching dynamic segments.
 *
 * Static segments are compared using canonical URI representations.
 *
 * Percent-encoded path separators remain encoded and do not change segment boundaries.
 * @since 1.0.0
 */
final class Path {

    /**
     * ### Matches a prepared request path against a route pattern
     *
     * Compares canonical static segments and extracts raw dynamic parameters.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Matcher\Path;
     * use FireHub\Adapter\HTTP\Router\Matcher\RequestPath;
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $path = RequestPath::parse('/users/123');
     *
     * if ($path !== null) {
     *
     *     $parameters = Path::match(
     *         new Pattern('/users/{id}'),
     *         $path
     *     );
     *
     *     // ['id' => '123']
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Path\Normalizer::segment() To normalize URI segments.
     * @uses \FireHub\Adapter\HTTP\Router\Path\Pattern::canonicalSegments() To get canonical route pattern segments.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher\RequestPath::canonicalSegments() To get canonical request path segments.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher\RequestPath::segments() To get request path segments.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count URI segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract named parameters from dynamic segments.
     *
     * @param \FireHub\Adapter\HTTP\Router\Path\Pattern $pattern <p>
     * Route pattern.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Matcher\RequestPath $path <p>
     * Prepared request path.
     * </p>
     *
     * @return null|array<non-empty-string, string> Extracted parameters, or null if unmatched.
     */
    public static function match (Pattern $pattern, RequestPath $path):?array {

        $segments = $path->segments();
        $canonical_segments = $path->canonicalSegments();
        $pattern_segments = $pattern->canonicalSegments();

        if (
            Runtime\Arr\Inspection::count($segments)
            !== Runtime\Arr\Inspection::count($pattern_segments)
        ) return null;

        /** @var array<non-empty-string, string> $parameters */
        $parameters = [];

        foreach ($pattern_segments as $index => $segment) {

            $value = $segments[$index] ?? null;
            $canonical = $canonical_segments[$index] ?? null;

            if ($value === null || $canonical === null)
                return null;

            if ($segment[0] === '{') {

                $name = Runtime\Str\SB\Access::part($segment, 1, -1);

                if ($name === '') return null;

                $parameters[$name] = $value;

            } else if ($segment !== $canonical) return null;

        }

        return $parameters;

    }

}