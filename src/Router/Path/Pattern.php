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

namespace FireHub\Adapter\HTTP\Router\Path;

use FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException;
use FireHub\Runtime;

/**
 * ### Represents a validated HTTP route path pattern
 *
 * Encapsulates an immutable route path pattern consisting of static segments and named parameters.
 *
 * Named parameters are enclosed in curly braces and must contain valid identifiers.
 *
 * Supported patterns:
 * <code>
 * /
 * /users
 * /users/{id}
 * /users/{id}/posts/{postId}
 * </code>
 *
 * Route patterns are case-sensitive and must begin with a forward slash.
 *
 * Empty segments, trailing slashes (except for the root path), query strings, fragments, and duplicate parameter
 * names are not permitted.
 *
 * Parameter identifiers must begin with an ASCII letter or underscore and may contain ASCII letters, digits, and
 * underscores.
 *
 * Route patterns describe matching criteria and do not perform HTTP request matching or parameter extraction.
 * @since 1.0.0
 */
final readonly class Pattern {

    /**
     * ### Route path segments
     * @since 1.0.0
     *
     * @var list<non-empty-string>
     */
    private array $segments;

    /**
     * ### Canonical route path segments
     * @since 1.0.0
     *
     * @var list<non-empty-string>
     */
    private array $canonical_segments;

    /**
     * ### Named route parameters
     * @since 1.0.0
     *
     * @var list<non-empty-string>
     */
    private array $parameters;

    /**
     * ### Constructor
     *
     * Creates a validated HTTP route path pattern.
     *
     * The pattern must begin with a forward slash and may contain static segments and named parameters.
     *
     * The root path is represented by a single forward slash and contains no segments.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $root = new Pattern('/');
     *
     * $users = new Pattern('/users');
     *
     * $user = new Pattern('/users/{id}');
     *
     * $post = new Pattern('/users/{id}/posts/{postId}');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Path\Pattern::parse() To validate and parse the route path pattern.
     * @uses \FireHub\Adapter\HTTP\Router\Path\Normalizer::segment() To normalize URI path segments.
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the pattern ends with a slash.
     *
     * @param non-empty-string $value <p>
     * HTTP route path pattern.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        if ($value === '' || $value[0] !== '/')
            throw new InvalidRoutePatternException(
                'Route path pattern must start with "/".'
            );

        if ($value === '/') {

            $this->segments = [];
            $this->canonical_segments = [];
            $this->parameters = [];

            return;

        }

        if (Runtime\Str\SB\Search::endsWith('/', $value))
            throw new InvalidRoutePatternException(
                'Route path pattern cannot end with "/".'
            );

        [$this->segments, $this->parameters] = self::parse($value);

        $canonical = [];

        foreach ($this->segments as $segment)
            $canonical[] = $segment[0] === '{'
                ? $segment
                : Normalizer::segment($segment);

        /** @var list<non-empty-string> $canonical */
        $this->canonical_segments = $canonical;

    }

    /**
     * ### Checks whether the route path pattern is dynamic
     *
     * Determines whether the route contains at least one named parameter.
     *
     * A dynamic route can match different path values by capturing parameter segments.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $static = new Pattern('/users');
     *
     * $static->isDynamic();
     *
     * // false
     *
     * $dynamic = new Pattern('/users/{id}');
     *
     * $dynamic->isDynamic();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the pattern contains named parameters, false otherwise.
     */
    public function isDynamic ():bool {

        return $this->parameters !== [];

    }

    /**
     * ### Gets the route path pattern
     *
     * Returns the original validated route path pattern.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $pattern = new Pattern('/users/{id}');
     *
     * $pattern->value();
     *
     * // '/users/{id}'
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Route path pattern.
     */
    public function value ():string {

        return $this->value;

    }

    /**
     * ### Gets the route path segments
     *
     * Returns the ordered route path segments.
     *
     * Static segments are returned as their original values, while named parameter segments retain their enclosing
     * curly braces.
     *
     * The root path contains no segments.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $pattern = new Pattern('/users/{id}/posts/{postId}');
     *
     * $pattern->segments();
     *
     * // ['users', '{id}', 'posts', '{postId}']
     *
     * $root = new Pattern('/');
     *
     * $root->segments();
     *
     * // []
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<non-empty-string> Route path segments.
     */
    public function segments ():array {

        return $this->segments;

    }

    /**
     * ### Gets the canonical route path segments
     *
     * Returns route path segments normalized for HTTP routing.
     *
     * Static segments use canonical percent-encoding.
     *
     * Dynamic parameter segments retain their original parameter names.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $pattern = new Pattern('/users/%41/{id}');
     *
     * $pattern->canonicalSegments();
     *
     * // ['users', 'A', '{id}']
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<non-empty-string> Canonical route path segments.
     */
    public function canonicalSegments ():array {

        return $this->canonical_segments;

    }

    /**
     * ### Gets the named route parameters
     *
     * Returns the names of all dynamic route parameters in their declaration order.
     *
     * Parameter names are returned without enclosing curly braces.
     *
     * Static route patterns return an empty array.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $pattern = new Pattern('/users/{id}/posts/{postId}');
     *
     * $pattern->parameters();
     *
     * // ['id', 'postId']
     *
     * $static = new Pattern('/users');
     *
     * $static->parameters();
     *
     * // []
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<non-empty-string> Named route parameter identifiers.
     */
    public function parameters ():array {

        return $this->parameters;

    }

    /**
     * ### Gets the structural route path signature
     *
     * Returns a normalized representation of the route pattern in which named parameters are replaced with anonymous
     * parameter placeholders.
     *
     * Parameter names do not affect the structural signature.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $first = new Pattern('/users/{id}');
     * $second = new Pattern('/users/{name}');
     * $static = new Pattern('/users/create');
     *
     * $first->signature();
     * // '/users/{}'
     *
     * $second->signature();
     * // '/users/{}'
     *
     * $static->signature();
     * // '/users/create'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join the segments with slashes.
     *
     * @return non-empty-string Structural route path signature.
     */
    public function signature ():string {

        if ($this->canonical_segments === []) return '/';

        $segments = [];

        foreach ($this->canonical_segments as $segment)
            $segments[] = $segment[0] === '{'
                ? '{}'
                : $segment;

        return '/' . Runtime\Str\SB\Delimiter::implode($segments, '/');

    }

    /**
     * ### Parses and validates route path segments
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if the pattern contains a query string or fragment.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the pattern into segments.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate parameter segments.
     * @uses \FireHub\Runtime\Arr\Inspection::inArray() To check for duplicate parameter names.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To remove the query string or fragment from the pattern.
     * @uses \FireHub\Runtime\DataIs::string() To validate parameter names.
     *
     * @param non-empty-string $value <p>
     * Route path pattern.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the pattern is invalid.
     *
     * @return array{
     *     list<non-empty-string>,
     *     list<non-empty-string>
     * } Parsed segments and parameter names.
     */
    private static function parse (string $value):array {

        if (
            Runtime\Str\SB\Search::contains('?', $value)
            || Runtime\Str\SB\Search::contains('#', $value)
        ) throw new InvalidRoutePatternException(
            'Route path pattern cannot contain a query string or fragment.'
        );

        $segments = []; $parameters = [];
        foreach (Runtime\Str\SB\Delimiter::explode(Runtime\Str\SB\Access::part($value, 1), '/') as $segment) {

            if ($segment === '')
                throw new InvalidRoutePatternException(
                    'Route path pattern cannot contain empty segments.'
                );

            /** @var null|array<string> $matches */
            $matches = null;

            if (Runtime\Str\SB\Regex::match('/\A\{([A-Za-z_][A-Za-z0-9_]*)}\z/', $segment, result: $matches)) {

                $name = $matches[1] ?? null;

                if (!Runtime\DataIs::string($name) || $name === '')
                    throw new InvalidRoutePatternException(
                        "Invalid route parameter segment '$segment'."
                    );

                if (Runtime\Arr\Inspection::inArray($parameters, $name))
                    throw new InvalidRoutePatternException(
                        "Duplicate route parameter '$name'."
                    );

                $parameters[] = $name;

            } else if (
                Runtime\Str\SB\Search::contains('{', $segment)
                || Runtime\Str\SB\Search::contains('}', $segment)
            ) throw new InvalidRoutePatternException(
                "Invalid route parameter segment '$segment'."
                );
            else if (
                !Runtime\Str\SB\Regex::match(
                    '/\A(?:[A-Za-z0-9._~!$&\'()*+,;=:@-]|%[A-Fa-f0-9]{2})+\z/',
                    $segment
                )
            ) throw new InvalidRoutePatternException(
                    "Invalid static route segment '$segment'."
                );

            $segments[] = $segment;

        }

        return [$segments, $parameters];

    }

}