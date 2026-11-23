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

namespace FireHub\Adapter\HTTP\Router;

use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Foundation\Uri\Component\Path;
use FireHub\Runtime;

/**
 * ### Matches HTTP requests against registered routes
 *
 * Resolves HTTP request methods and URI paths against route definitions provided by a storage implementation.
 *
 * Static routes take precedence over dynamic routes.
 *
 * Named parameters are extracted from matching dynamic segments.
 *
 * The matcher does not execute route handlers or generate HTTP responses.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Router\Matcher;
 * use FireHub\Adapter\HTTP\Router\Storage\Map;
 * use FireHub\Adapter\HTTP\Request\Method\Standard;
 * use FireHub\Foundation\Uri\Component\Path;
 *
 * $matcher = new Matcher($storage);
 *
 * $match = $matcher->match(
 *     Standard::GET,
 *     new Path('/users/123')
 * );
 *
 * $match?->parameter('id');
 *
 * // '123'
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Matcher {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Router\Storage $storage <p>
     * Route storage implementation.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Storage $storage
    ) {}

    /**
     * ### Matches an HTTP request
     *
     * Searches registered route candidates for a matching request method and URI path.
     *
     * Static routes take precedence over dynamic routes.
     *
     * Returns null when no route matches.
     *
     * <code>
     * $match = $matcher->match(
     *     Standard::GET,
     *     new Path('/users/123/posts/456')
     * );
     *
     * $match?->parameters();
     *
     * // ['id' => '123', 'post' => '456']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::candidates() To search for route candidates.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::matchSegments() To extract named parameters.
     * @uses \FireHub\Foundation\Uri\Component\Path::normalized() To normalize the request path.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the normalized path into segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To access the normalized path segment.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To determine the number of path segments.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path<p>
     * HTTP request path.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Router\RouteMatch Matched route or null.
     */
    public function match (Method $method, Path $path):?RouteMatch {

        $normalized = $path->normalized();

        $segments = $normalized === '/'
            ? []
            : Runtime\Str\SB\Delimiter::explode(
                Runtime\Str\SB\Access::part($normalized, 1),
                '/'
            );

        $static_match = null;
        $dynamic_match = null;
        $static_path_exists = false;

        foreach ($this->storage->candidates($path) as $route) {

            $parameters = $this->matchSegments(
                $route->pattern(),
                $segments
            );

            if ($parameters === null)
                continue;

            if (!$route->pattern()->isDynamic()) {

                $static_path_exists = true;

                if ($route->method()->value() === $method->value())
                    $static_match = new RouteMatch($route, []);

                continue;

            }

            if (
                $dynamic_match === null
                && $route->method()->value() === $method->value()
            ) $dynamic_match = new RouteMatch(
                $route,
                $parameters
            );

        }

        if ($static_path_exists)
            return $static_match;

        return $dynamic_match;

    }

    /**
     * ### Matches route pattern segments
     *
     * Compares canonical route segments with normalized request path segments and extracts named parameters.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Pattern::canonicalSegments() To retrieve the canonical route segments.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To determine the number of segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To access the segment.
     *
     * @param \FireHub\Adapter\HTTP\Router\Pattern $pattern <p>
     * Route pattern.
     * </p>
     * @param list<string> $segments <p>
     * Request path segments.
     * </p>
     *
     * @return null|array<non-empty-string, string> Extracted parameters
     * or null when the pattern does not match.
     */
    private function matchSegments (Pattern $pattern, array $segments):?array {

        $pattern_segments = $pattern->canonicalSegments();

        if (
            Runtime\Arr\Inspection::count($pattern_segments)
            !== Runtime\Arr\Inspection::count($segments)
        ) return null;

        $parameters = [];

        foreach ($pattern_segments as $index => $segment) {

            if (!isset($segments[$index]))
                return null;

            $value = $segments[$index];

            if ($segment[0] === '{') {

                if ($value === '')
                    return null;

                /** @var non-empty-string $name */
                $name = Runtime\Str\SB\Access::part(
                    $segment,
                    1,
                    -1
                );

                $parameters[$name] = $value;

                continue;

            }

            if ($segment !== $value)
                return null;

        }

        return $parameters;

    }

}