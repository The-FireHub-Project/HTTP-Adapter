<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Router\Storage;

use FireHub\Adapter\HTTP\Router\ {
    Route\Name, Route, Storage
};
use FireHub\Foundation\DataStructure\ {
    Factory\MapFactory, Factory\SetFactory,
    Map as MapDS, Set as SetDS
};
use FireHub\Foundation\Uri\Component\Path;
use FireHub\Adapter\HTTP\Router\Exception\ {
    DuplicateRouteException, DuplicateRouteNameException
};
use FireHub\Runtime;

/**
 * ### Provides map-based HTTP route storage
 *
 * Stores HTTP route definitions using Foundation Map data structures.
 *
 * Static routes are indexed by their normalized request paths, while dynamic routes are grouped by their number of
 * path segments.
 *
 * Route registration rejects duplicate method and structural pattern combinations.
 *
 * Candidate retrieval is independent of the HTTP request method, allowing the matcher to detect method mismatches.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Router\Storage\Map;
 * use FireHub\Adapter\HTTP\Router\Route;
 * use FireHub\Adapter\HTTP\Router\Pattern;
 * use FireHub\Adapter\HTTP\Request\Method\Standard;
 * use FireHub\Foundation\Uri\Component\Path;
 *
 * $storage = new Map();
 *
 * $storage->add(
 *     new Route(
 *         Standard::GET,
 *         new Pattern('/users/{id}'),
 *         UserController::class
 *     )
 * );
 *
 * $candidates = $storage->candidates(
 *     new Path('/users/123')
 * );
 * </code>
 *
 * @since 1.0.0
 */
final class Map implements Storage {

    /**
     * ### Static route index
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\DataStructure\Map<string, list<\FireHub\Adapter\HTTP\Router\Route>>
     */
    private MapDS $static_routes;

    /**
     * ### Dynamic route index
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\DataStructure\Map<int, list<\FireHub\Adapter\HTTP\Router\Route>>
     */
    private MapDS $dynamic_routes;

    /**
     * ### Registered route signatures
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\DataStructure\Set<string>
     */
    private SetDS $signatures;

    /**
     * ### Named route index
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\DataStructure\Map<string, \FireHub\Adapter\HTTP\Router\Route>
     */
    private MapDS $named_routes;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Factory\MapFactory::arr() To initialize the map indexes.
     * @uses \FireHub\Foundation\DataStructure\Factory\SetFactory::arr() To initialize the signature set.
     *
     * @return void
     */
    public function __construct () {

        $map_factory = new MapFactory;
        $set_factory = new SetFactory;

        /** @var \FireHub\Foundation\DataStructure\Map<string, list<\FireHub\Adapter\HTTP\Router\Route>> $static */
        $static = $map_factory->arr([]);

        /** @var \FireHub\Foundation\DataStructure\Map<int, list<\FireHub\Adapter\HTTP\Router\Route>> $dynamic */
        $dynamic = $map_factory->arr([]);

        /** @var \FireHub\Foundation\DataStructure\Set<string> $signatures */
        $signatures = $set_factory->arr([]);

        /** @var \FireHub\Foundation\DataStructure\Map<string, \FireHub\Adapter\HTTP\Router\Route> $named */
        $named = $map_factory->arr([]);

        $this->static_routes = $static;
        $this->dynamic_routes = $dynamic;
        $this->signatures = $signatures;
        $this->named_routes = $named;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $storage->add(
     *     new Route(
     *         Standard::GET,
     *         new Pattern('/users/{id}'),
     *         UserController::class
     *     )
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Route::method() To identify method-specific routes.
     * @uses \FireHub\Adapter\HTTP\Router\Route::pattern() To identify structural routes.
     * @uses \FireHub\Adapter\HTTP\Router\Pattern::signature() To identify structurally equivalent patterns.
     * @uses \FireHub\Adapter\HTTP\Router\Pattern::isDynamic() To select the appropriate route index.
     * @uses \FireHub\Foundation\Uri\Component\Path::canonicalSegments() To build a canonical path.
     * @uses \FireHub\Foundation\DataStructure\Map::get() To retrieve the route index.
     * @uses \FireHub\Foundation\DataStructure\Map::set() To update the route indexes.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check for duplicate signatures.
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteNameException If a route with the same name is
     * already registered.
     */
    public function add (Route $route):void {

        $pattern = $route->pattern();
        $name = $route->name();

        $signature = $route->method()->value()
            . ':'
            . $pattern->signature();

        if ($this->signatures->contains($signature))
            throw new DuplicateRouteException;

        if ($name !== null) {

            $existing = $this->named_routes->get(
                $name->value()
            );

            if ($existing->isSome())
                throw new DuplicateRouteNameException;

        }

        if ($pattern->isDynamic()) {

            $key = Runtime\Arr\Inspection::count(
                $pattern->segments()
            );

            $existing = $this->dynamic_routes->get($key);

            /** @var list<\FireHub\Adapter\HTTP\Router\Route> $routes */
            $routes = $existing->isSome()
                ? $existing->value()
                : [];

            $routes[] = $route;

            $this->dynamic_routes->set($key, $routes);

        } else {

            $key = $this->canonicalPath(
                $pattern->canonicalSegments()
            );

            $existing = $this->static_routes->get($key);

            /** @var list<\FireHub\Adapter\HTTP\Router\Route> $routes */
            $routes = $existing->isSome()
                ? $existing->value()
                : [];

            $routes[] = $route;

            $this->static_routes->set($key, $routes);

        }

        $this->signatures->add($signature);

        if ($name !== null)
            $this->named_routes->set(
                $name->value(),
                $route
            );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Uri\Component\Path;
     *
     * $candidates = $storage->candidates(
     *     new Path('/users/123')
     * );
     *
     * foreach ($candidates as $route) {
     *     // Evaluate route pattern.
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Path::normalized() To normalize the request path.
     * @uses \FireHub\Foundation\DataStructure\Map::get() To retrieve indexed route candidates.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the normalized path into segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To access the normalized path segment.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To determine the number of path segments.
     */
    public function candidates (Path $path):array {

        $normalized = $path->normalized();

        $candidates = [];

        $static = $this->static_routes->get($normalized);

        if ($static->isSome()) {

            /** @var list<\FireHub\Adapter\HTTP\Router\Route> $routes */
            $routes = $static->value();

            foreach ($routes as $route)
                $candidates[] = $route;

        }

        $segments = $normalized === '/'
            ? []
            : Runtime\Str\SB\Delimiter::explode(Runtime\Str\SB\Access::part($normalized, 1), '/');

        $dynamic = $this->dynamic_routes->get(
            Runtime\Arr\Inspection::count($segments)
        );

        if ($dynamic->isSome()) {

            /** @var list<\FireHub\Adapter\HTTP\Router\Route> $routes */
            $routes = $dynamic->value();

            foreach ($routes as $route)
                $candidates[] = $route;

        }

        return $candidates;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $route = $storage->findByName(
     *     new Name('users.show')
     * );
     *
     * if ($route !== null)
     *     echo $route->pattern()->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Map::get() To retrieve the named route.
     */
    public function findByName (Name $name):?Route {

        $route = $this->named_routes->get(
            $name->value()
        );

        /** @var null|\FireHub\Adapter\HTTP\Router\Route */
        return $route->isSome()
            ? $route->value()
            : null;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $routes = $storage->routes();
     *
     * foreach ($routes as $route) {
     *     echo $route->pattern()->value();
     * }
     * </code>
     *
     * @since 1.0.0
     */
    public function routes ():array {

        $routes = [];

        foreach ($this->static_routes as $group)
            foreach ($group as $route)
                $routes[] = $route;

        foreach ($this->dynamic_routes as $group)
            foreach ($group as $route)
                $routes[] = $route;

        return $routes;

    }

    /**
     * ### Builds a canonical path
     *
     * Combines canonical path segments into an absolute path.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join the segments into a string.
     *
     * @param list<non-empty-string> $segments <p>
     * Canonical path segments.
     * </p>
     *
     * @return non-empty-string Canonical absolute path.
     */
    private function canonicalPath (array $segments):string {

        if ($segments === [])
            return '/';

        return '/' . Runtime\Str\SB\Delimiter::implode($segments, '/');

    }

}