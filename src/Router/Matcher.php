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
use FireHub\Adapter\HTTP\Request\Method\Standard;
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
     * Resolves the most specific route pattern matching the request path, then selects the route matching the
     * requested HTTP method.
     *
     * Route specificity is independent of the HTTP method.
     *
     * <code>
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
     *
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::matchingRoutes() To retrieve matching routes.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::compareSpecificity() To compare route specificity.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * HTTP request path.
     * </p>
     *
     * @return null|RouteMatch Matched route or null.
     */
    public function match (Method $method, Path $path):?RouteMatch {

        $matches = $this->bestMatches($path);

        if ($matches === [])
            return null;

        foreach ($matches as $match) {

            if ($match->route()->method()->value() === $method->value())
                return $match;

        }

        if ($method->value() !== Standard::HEAD->value)
            return null;

        foreach ($matches as $match) {

            if ($match->route()->method()->value() === Standard::GET->value)
                return $match;

        }

        return null;

    }

    /**
     * ### Gets allowed HTTP methods for a request path
     *
     * Returns explicitly registered HTTP methods for the most specific matching route pattern.
     *
     * Implicit HEAD and OPTIONS methods are not included.
     *
     * <code>
     * $methods = $matcher->allowedMethods(
     *     new Path('/users/123')
     * );
     *
     * // [Standard::GET, Standard::POST]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::bestMatches() To retrieve the most specific matching routes.
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To compare HTTP method names.
     *
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * HTTP request path.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Request\Method> Explicitly registered HTTP methods.
     */
    public function allowedMethods (Path $path):array {

        $methods = []; $seen = [];
        foreach ($this->bestMatches($path) as $match) {

            $method = $match->route()->method();

            $name = $method->value();

            if (isset($seen[$name]))
                continue;

            $seen[$name] = true;

            $methods[] = $method;

        }

        return $methods;

    }

    /**
     * ### Gets the most specific matching routes
     *
     * Resolves all routes matching the request path and retains only those with the highest route pattern specificity.
     *
     * HTTP methods do not affect route specificity.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::matchingRoutes() To retrieve matching routes.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::compareSpecificity() To compare route specificity.
     *
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * HTTP request path.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Router\RouteMatch> Most specific matching routes.
     */
    private function bestMatches (Path $path):array {

        $matches = $this->matchingRoutes($path);

        if ($matches === [])
            return [];

        $best = $matches[0];

        foreach ($matches as $match) {

            if (
                $this->compareSpecificity(
                    $match->route()->pattern(),
                    $best->route()->pattern()
                ) > 0
            ) $best = $match;

        }

        $best_matches = [];

        foreach ($matches as $match) {

            if (
                $this->compareSpecificity(
                    $match->route()->pattern(),
                    $best->route()->pattern()
                ) === 0
            ) $best_matches[] = $match;

        }

        return $best_matches;

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

    /**
     * ### Compares route pattern specificity
     *
     * Compares two route patterns from left to right.
     *
     * A static segment has higher priority than a dynamic segment at the first position where the patterns differ.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Pattern::canonicalSegments() To retrieve the canonical route segments.
     *
     * @param \FireHub\Adapter\HTTP\Router\Pattern $first <p>
     * First route pattern.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Pattern $second <p>
     * Second route pattern.
     * </p>
     *
     * @return int<-1, 1> Positive if the first pattern has higher priority, negative if the second has higher
     * priority, or zero if equal.
     */
    private function compareSpecificity (Pattern $first, Pattern $second):int {

        $first_segments = $first->canonicalSegments();
        $second_segments = $second->canonicalSegments();

        foreach ($first_segments as $index => $segment) {

            if (!isset($second_segments[$index]))
                break;

            $first_dynamic = $segment[0] === '{';
            $second_dynamic = $second_segments[$index][0] === '{';

            if ($first_dynamic === $second_dynamic)
                continue;

            return $first_dynamic ? -1 : 1;

        }

        return 0;

    }

    /**
     * ### Gets routes matching a request path
     *
     * Matches route patterns independently of HTTP methods.
     *
     * Extracts named parameters for every matching route.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::matchSegments() To match route segments.
     * @uses \FireHub\Adapter\HTTP\Router\Storage::candidates() To retrieve candidate routes.
     * @uses \FireHub\Foundation\Uri\Component\Path::normalized() To retrieve the normalized path.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the normalized path into segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To access the segment.
     *
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * HTTP request path.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Router\RouteMatch> Routes matching the request path.
     */
    private function matchingRoutes (Path $path):array {

        $normalized = $path->normalized();

        $segments = $normalized === '/'
            ? []
            : Runtime\Str\SB\Delimiter::explode(
                Runtime\Str\SB\Access::part($normalized, 1),
                '/'
            );

        $matches = [];

        foreach ($this->storage->candidates($path) as $route) {

            $parameters = $this->matchSegments(
                $route->pattern(),
                $segments
            );

            if ($parameters === null)
                continue;

            $matches[] = new RouteMatch(
                $route,
                $parameters
            );

        }

        return $matches;

    }

}