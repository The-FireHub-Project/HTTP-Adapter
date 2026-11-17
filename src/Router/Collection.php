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

namespace FireHub\Adapter\HTTP\Router;

use FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException;

/**
 * ### Represents a collection of registered HTTP routes
 *
 * Stores HTTP route definitions and ensures that each combination of HTTP method and path pattern is unique.
 *
 * Named routes must also have unique names within the collection.
 *
 * Route registration preserves insertion order.
 *
 * The collection does not perform route matching or request execution.
 * @since 1.0.0
 */
final class Collection {

    /**
     * ### Registered routes
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Router\Route>
     */
    private array $routes = [];

    /**
     * ### Registered route identifiers
     * @since 1.0.0
     *
     * @var array<string, true>
     */
    private array $identifiers = [];

    /**
     * ### Registered route names
     * @since 1.0.0
     *
     * @var array<string, true>
     */
    private array $names = [];

    /**
     * ### Registers an HTTP route
     *
     * Adds a route to the collection.
     *
     * Routes with duplicate HTTP methods and path patterns are rejected. Duplicate route names are also rejected.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Collection;
     * use FireHub\Adapter\HTTP\Router\Route;
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     * use FireHub\Adapter\HTTP\Request\Method\Standard;
     *
     * $routes = new Collection();
     *
     * $routes->add(
     *     new Route(
     *         Standard::GET,
     *         new Pattern('/users'),
     *         'users.index'
     *     )
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Route::method() To get the HTTP method.
     * @uses \FireHub\Adapter\HTTP\Router\Route::path() To get the path pattern.
     * @uses \FireHub\Adapter\HTTP\Router\Route::name() To get the route name.
     *
     * @param \FireHub\Adapter\HTTP\Router\Route $route <p>
     * Route to register.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If the route method, path, or name
     * conflicts with an existing route.
     *
     * @return void
     */
    public function add (Route $route):void {

        $identifier = $route->method()->value() . ' ' . $route->path()->signature();

        if (isset($this->identifiers[$identifier]))
            throw new DuplicateRouteException(
                "Route '$identifier' is already registered."
            );

        $name = $route->name();

        if ($name !== null && isset($this->names[$name]))
            throw new DuplicateRouteException(
                "Route name '$name' is already registered."
            );

        $this->routes[] = $route;
        $this->identifiers[$identifier] = true;

        if ($name !== null)
            $this->names[$name] = true;

    }

    /**
     * ### Gets all registered HTTP routes
     *
     * Returns route definitions in their original registration order.
     *
     * <code>
     * $routes = new Collection();
     *
     * $routes->add(
     *     new Route(Standard::GET, new Pattern('/users'))
     * );
     *
     * $routes->all();
     *
     * // [Route]
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Router\Route> Registered HTTP routes.
     */
    public function all ():array {

        return $this->routes;

    }

}