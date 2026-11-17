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
use FireHub\Adapter\HTTP\Router\Path\Pattern;
use FireHub\Adapter\HTTP\Router\MatchResult\ {
    Matched, MethodNotAllowed, NotFound
};
use FireHub\Runtime;

/**
 * ### Matches HTTP requests against registered routes
 *
 * Resolves HTTP methods and request paths to registered route definitions.
 *
 * Static route segments take precedence over dynamic parameter segments.
 *
 * The matcher distinguishes successful matches, missing routes, and unsupported HTTP methods.
 *
 * Route matching is case-sensitive.
 * @since 1.0.0
 */
final readonly class Matcher {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Router\Collection $routes <p>
     * Collection containing registered HTTP routes.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Collection $routes
    ) {}

    /**
     * ### Matches an HTTP method and request path
     *
     * Finds the most specific route path matching the requested path and then checks whether the requested HTTP
     * method is supported.
     *
     * Static segments take precedence over dynamic segments, regardless of registration order.
     *
     * If the most specific matching path does not support the requested method, a MethodNotAllowed result is
     * returned with the methods available for that path pattern.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Collection;
     * use FireHub\Adapter\HTTP\Router\Matcher;
     * use FireHub\Adapter\HTTP\Router\Route;
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     * use FireHub\Adapter\HTTP\Router\MatchResult\Matched;
     * use FireHub\Adapter\HTTP\Request\Method\Standard;
     *
     * $routes = new Collection();
     *
     * $routes->add(
     *     new Route(Standard::GET, new Pattern('/users/{id}'))
     * );
     *
     * $routes->add(
     *     new Route(Standard::POST, new Pattern('/users/create'))
     * );
     *
     * $matcher = new Matcher($routes);
     *
     * $result = $matcher->match(Standard::POST, '/users/create');
     *
     * // Matched: POST /users/create
     *
     * $result = $matcher->match(Standard::GET, '/users/create');
     *
     * // MethodNotAllowed: POST
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Collection::all() To get all registered routes.
     * @uses \FireHub\Adapter\HTTP\Router\MatchResult\Matched::route() To get the matched route.
     * @uses \FireHub\Adapter\HTTP\Router\MatchResult\Matched::parameters() To get the extracted parameters.
     * @uses \FireHub\Adapter\HTTP\Router\MatchResult\MethodNotAllowed::methods() To get the allowed HTTP methods.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::matchPath() To match the path against a route pattern.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::score() To calculate route specificity.
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the allowed methods list.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * Requested HTTP method.
     * </p>
     * @param string $path <p>
     * Requested URL path without query string or fragment.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Router\MatchResult Route matching result.
     */
    public function match (Method $method, string $path):MatchResult {

        /** @var null|Path\Pattern $best_pattern */
        $best_pattern = null;

        /** @var list<array{Route, array<non-empty-string, string>}> $matches */
        $matches = [];

        foreach ($this->routes->all() as $route) {

            $parameters = self::matchPath($route->path(), $path);

            if ($parameters === null) continue;

            $pattern = $route->path();

            if ($best_pattern !== null) {

                $comparison = self::compare($pattern, $best_pattern);

                if ($comparison < 0) continue;

                if ($comparison > 0) $matches = [];

            }

            $best_pattern = $pattern;

            $matches[] = [$route, $parameters];

        }

        if ($matches === []) return new NotFound();

        /** @var array<string, Method> $allowed */
        $allowed = [];

        foreach ($matches as [$route, $parameters]) {

            $route_method = $route->method();

            $allowed[$route_method->value()] = $route_method;

            if ($route_method->value() === $method->value())
                return new Matched($route, $parameters);

        }

        return new MethodNotAllowed(...Runtime\Arr\Access::values($allowed));

    }

    /**
     * ### Matches a request path against a route pattern
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Path\Pattern::value() To get route path value.
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the path ends with a slash.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the path into segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get the path segment.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check the segment count.
     *
     * @param \FireHub\Adapter\HTTP\Router\Path\Pattern $pattern <p>
     * Route path pattern.
     * </p>
     * @param string $path <p>
     * Requested path.
     * </p>
     *
     * @return null|array<non-empty-string, string> Extracted parameters, or null if unmatched.
     */
    private static function matchPath (Pattern $pattern, string $path):?array {

        if ($path === '/')
            return $pattern->value() === '/' ? [] : null;

        if ($path === '' || $path[0] !== '/' || Runtime\Str\SB\Search::endsWith('/', $path))
            return null;

        $segments = Runtime\Str\SB\Delimiter::explode(Runtime\Str\SB\Access::part($path, 1), '/');
        $pattern_segments = $pattern->segments();

        if (Runtime\Arr\Inspection::count($segments) !== Runtime\Arr\Inspection::count($pattern_segments))
            return null;

        $parameters = [];

        foreach ($pattern_segments as $index => $segment) {

            $value = $segments[$index] ?? null;

            if ($value === null || $value === '') return null;

            if ($segment[0] === '{') {

                $name = Runtime\Str\SB\Access::part($segment, 1, -1);

                if ($name === '') return null;

                $parameters[$name] = $value;

            } else if ($segment !== $value) return null;

        }

        return $parameters;

    }

    /**
     * ### Compares route path specificity
     *
     * Compares route patterns from left to right.
     *
     * Static segments are considered more specific than dynamic parameter segments.
     *
     * The first differing segment determines the result.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Path\Pattern::segments() To get route path segments.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the segment count.
     *
     * @param \FireHub\Adapter\HTTP\Router\Path\Pattern $first <p>
     * First route path pattern.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Path\Pattern $second <p>
     * Second route path pattern.
     * </p>
     *
     * @return int<-1, 1> Positive if the first pattern is more specific, negative if the second is more specific, or
     * zero if both have equal specificity.
     */
    private static function compare (Pattern $first, Pattern $second):int {

        $first_segments = $first->segments();
        $second_segments = $second->segments();

        foreach ($first_segments as $index => $segment) {

            $other = $second_segments[$index] ?? null;

            if ($other === null) return 1;

            $first_dynamic = $segment[0] === '{';
            $second_dynamic = $other[0] === '{';

            if ($first_dynamic === $second_dynamic) continue;

            return $first_dynamic ? -1 : 1;

        }

        if (Runtime\Arr\Inspection::count($first_segments) < Runtime\Arr\Inspection::count($second_segments))
            return -1;

        return 0;

    }

}