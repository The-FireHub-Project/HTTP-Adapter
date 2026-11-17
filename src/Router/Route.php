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

/**
 * ### Represents an immutable HTTP route definition
 *
 * Defines the HTTP method and path pattern used to identify an HTTP route.
 *
 * A route describes matching criteria independently of HTTP request execution.
 *
 * Route definitions do not manage request handlers, dependency injection scopes, or HTTP responses.
 *
 * Path patterns may contain named parameters enclosed in curly braces.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Router\Route;
 * use FireHub\Adapter\HTTP\Router\Path\Pattern;
 * use FireHub\Adapter\HTTP\Request\Method\Standard;
 *
 * $route = new Route(
 *     Standard::GET,
 *     new Pattern('/users/{id}'),
 *     'users.show'
 * );
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Route {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP method associated with the route.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Path\Pattern $path <p>
     * Validated route path pattern.
     * </p>
     * @param null|non-empty-string $name [optional] <p>
     * Optional unique route name.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Method $method,
        private Pattern $path,
        private ?string $name = null
    ) {}

    /**
     * ### Gets the HTTP method
     *
     * Returns the HTTP method associated with this route.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Route;
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     * use FireHub\Adapter\HTTP\Request\Method\Standard;
     *
     * $route = new Route(
     *     Standard::GET,
     *     new Pattern('/users')
     * );
     *
     * $route->method();
     *
     * // Standard::GET
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request\Method HTTP method.
     */
    public function method ():Method {

        return $this->method;

    }

    /**
     * ### Gets the route path pattern
     *
     * Returns the validated path pattern associated with this route.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Route;
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     * use FireHub\Adapter\HTTP\Request\Method\Standard;
     *
     * $route = new Route(
     *     Standard::GET,
     *     new Pattern('/users/{id}')
     * );
     *
     * $route->path()->value();
     *
     * // '/users/{id}'
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Router\Path\Pattern Route path pattern.
     */
    public function path ():Pattern {

        return $this->path;

    }

    /**
     * ### Gets the route name
     *
     * Returns the optional name assigned to this route.
     *
     * Unnamed routes return null.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Route;
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     * use FireHub\Adapter\HTTP\Request\Method\Standard;
     *
     * $route = new Route(
     *     Standard::GET,
     *     new Pattern('/users/{id}'),
     *     'users.show'
     * );
     *
     * $route->name();
     *
     * // 'users.show'
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|non-empty-string Route name, or null if unnamed.
     */
    public function name ():?string {

        return $this->name;

    }

}