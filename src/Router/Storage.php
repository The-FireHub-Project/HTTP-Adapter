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

use FireHub\Adapter\HTTP\Router\Route\Name;
use FireHub\Foundation\Uri\Component\Path;

/**
 * ### Defines HTTP route storage capabilities
 *
 * Provides a storage-independent contract for registering HTTP routes and retrieving candidates for request path
 * matching.
 *
 * Storage implementations are responsible for indexing route definitions but do not perform HTTP request matching or
 * handler execution.
 *
 * Candidate retrieval must preserve all potentially matching routes. Implementations may use different indexing
 * algorithms but must provide equivalent matching behavior.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Router\Storage\Map;
 * use FireHub\Foundation\Uri\Component\Path;
 *
 * $storage = new Map();
 *
 * $storage->add($route);
 *
 * $candidates = $storage->candidates(
 *     new Path('/users/123')
 * );
 *
 * foreach ($candidates as $candidate) {
 *     // Evaluate candidate.
 * }
 * </code>
 *
 * @since 1.0.0
 */
interface Storage {

    /**
     * ### Registers an HTTP route
     *
     * Adds a route definition to the underlying storage.
     *
     * Implementations must reject duplicate route definitions that share the same HTTP method and structural route
     * signature.
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
     * @param \FireHub\Adapter\HTTP\Router\Route $route <p>
     * Route definition to register.
     * </p>
     *
     * @return void
     */
    public function add (Route $route):void;

    /**
     * ### Gets route candidates for a request path
     *
     * Retrieves route definitions that may match the specified request path.
     *
     * Candidate retrieval is independent of the HTTP request method, allowing the matcher to detect method
     * mismatches and construct appropriate HTTP responses.
     *
     * Implementations must not exclude potentially matching routes.
     *
     * <code>
     * use FireHub\Foundation\Uri\Component\Path;
     *
     * $candidates = $storage->candidates(
     *     new Path('/users/123')
     * );
     *
     * foreach ($candidates as $route) {
     *     // Evaluate the route pattern.
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * HTTP request path.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Router\Route> Potentially matching routes.
     */
    public function candidates (Path $path):array;

    /**
     * ### Finds a registered route by name
     *
     * Returns the route associated with the given name.
     *
     * Route names are case-sensitive.
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
     * @param \FireHub\Adapter\HTTP\Router\Route\Name $name <p>
     * Name of the registered route.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Router\Route Matching route, or null if not found.
     */
    public function findByName (Name $name):?Route;

    /**
     * ### Gets all registered routes
     *
     * Returns the registered route definitions.
     *
     * <code>
     * $routes = $storage->routes();
     *
     * foreach ($routes as $route) {
     *     // Inspect route definition.
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Router\Route> Registered route definitions.
     */
    public function routes ():array;

}