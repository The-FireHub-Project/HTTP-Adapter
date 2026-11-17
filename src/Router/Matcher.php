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
use FireHub\Adapter\HTTP\Router\Matcher\ {
    AllowedMethods, Path, RequestPath, Specificity
};
use FireHub\Adapter\HTTP\Router\MatchResult\ {
    Matched, MethodNotAllowed, NotFound, Options
};
use FireHub\Adapter\HTTP\Request\Method\Standard;
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
     * Prepares the request path at once and selects the most specific registered route supporting the requested HTTP
     * method.
     *
     * <code>
     * $matcher = new Matcher($routes);
     *
     * $result = $matcher->match(
     *     Standard::GET,
     *     '/users/123'
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Matcher\RequestPath::parse() To parse the request path.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::findCandidates() To find matching routes.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::resolveMethod() To resolve HTTP methods.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * Requested HTTP method.
     * </p>
     * @param string $path <p>
     * Requested URI path.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Router\MatchResult Route matching result.
     */
    public function match (Method $method, string $path):MatchResult {

        $request_path = RequestPath::parse($path);

        if ($request_path === null)
            return new NotFound();

        $candidates = $this->findCandidates($request_path);

        if ($candidates === [])
            return new NotFound();

        return self::resolveMethod($method, $candidates);

    }

    /**
     * ### Finds the most specific matching routes
     *
     * Finds all registered routes whose path patterns match the requested path and have the highest specificity.
     *
     * Routes with lower specificity are excluded regardless of registration order.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Collection::all() To retrieve registered routes.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher\Path::match() To match request paths.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher\Specificity::compare() To compare route specificity.
     *
     * @param \FireHub\Adapter\HTTP\Router\Matcher\RequestPath $path <p>
     * Requested URI path.
     * </p>
     *
     * @return list<array{
     *     \FireHub\Adapter\HTTP\Router\Route,
     *     array<non-empty-string, string>
     * }> Matching routes and their extracted parameters.
     */
    private function findCandidates (RequestPath $path):array {

        /** @var null|\FireHub\Adapter\HTTP\Router\Path\Pattern $best_pattern */
        $best_pattern = null;

        /** @var list<array{\FireHub\Adapter\HTTP\Router\Route, array<non-empty-string, string>}> $matches */
        $matches = [];

        foreach ($this->routes->all() as $route) {

            $pattern = $route->path();

            $parameters = Path::match($pattern, $path);

            if ($parameters === null) continue;

            if ($best_pattern !== null) {

                $comparison = Specificity::compare($pattern, $best_pattern);

                if ($comparison < 0) continue;

                if ($comparison > 0) $matches = [];

            }

            $best_pattern = $pattern;

            $matches[] = [$route, $parameters];

        }

        return $matches;

    }

    /**
     * ### Resolves the requested HTTP method
     *
     * Explicit route methods take precedence over implicit HTTP method handling.
     *
     * HEAD falls back to GET when no explicit HEAD route matches.
     *
     * OPTIONS are automatically supported for registered route paths.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Matcher\AllowedMethods::resolve() To resolve allowed HTTP methods.
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To retrieve the HTTP method value.
     * @uses \FireHub\Adapter\HTTP\Router\Route::method() To retrieve the route method.
     * @uses \FireHub\Runtime\Arr\Mutation::shift() To remove the first element from an array.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * Requested HTTP method.
     * </p>
     * @param non-empty-list<array{\FireHub\Adapter\HTTP\Router\Route, array<non-empty-string, string>}> $candidates <p>
     * Matching route candidates.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Router\MatchResult Route matching result.
     */
    private static function resolveMethod (Method $method, array $candidates):MatchResult {

        /** @var list<\FireHub\Adapter\HTTP\Request\Method> $registered */
        $registered = [];

        /** @var null|array{\FireHub\Adapter\HTTP\Router\Route, array<non-empty-string, string>} $get_match */
        $get_match = null;

        $requested_method = $method->value();

        foreach ($candidates as [$route, $parameters]) {

            $route_method = $route->method();
            $route_method_value = $route_method->value();

            $registered[] = $route_method;

            if ($route_method_value === $requested_method)
                return new Matched($route, $parameters);

            if (
                $requested_method === Standard::HEAD->value
                && $route_method_value === Standard::GET->value
            ) $get_match = [$route, $parameters];

        }

        if ($get_match !== null)
            return new Matched($get_match[0], $get_match[1]);

        $first = Runtime\Arr\Mutation::shift($registered);

        if ($first === null) return new NotFound();

        $allowed = AllowedMethods::resolve($first, ...$registered);

        if ($requested_method === Standard::OPTIONS->value)
            return new Options($allowed);

        $first_allowed = Runtime\Arr\Mutation::shift($allowed);

        if ($first_allowed === null) return new NotFound();

        return new MethodNotAllowed($first_allowed, ...$allowed);

    }

}