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

/**
 * ### Represents a successful HTTP route match
 *
 * Contains the matched route and the named parameters extracted from the request path.
 *
 * <code>
 * $match->route();
 *
 * $match->parameters();
 *
 * // ['id' => '123']
 *
 * $match->parameter('id');
 *
 * // '123'
 * </code>
 *
 * @since 1.0.0
 */
final readonly class RouteMatch {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Router\Route $route <p>
     * Matched route.
     * </p>
     * @param array<non-empty-string, string> $parameters <p>
     * Named route parameters.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Route $route,
        private array $parameters = []
    ) {}

    /**
     * ### Gets the matched route
     *
     * <code>
     * $match->route()->pattern()->value();
     *
     * // '/users/{id}'
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Router\Route Matched route.
     */
    public function route ():Route {

        return $this->route;

    }

    /**
     * ### Gets all route parameters
     *
     * <code>
     * $match->parameters();
     *
     * // ['id' => '123']
     * </code>
     *
     * @since 1.0.0
     *
     * @return array<non-empty-string, string> Named route parameters.
     */
    public function parameters ():array {

        return $this->parameters;

    }

    /**
     * ### Gets a named route parameter
     *
     * Returns null when the requested parameter does not exist.
     *
     * <code>
     * $match->parameter('id');
     *
     * // '123'
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $name Parameter name.
     *
     * @return null|string Parameter value.
     */
    public function parameter (string $name):?string {

        return $this->parameters[$name] ?? null;

    }

}