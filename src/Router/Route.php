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
use Closure;

/**
 * ### Represents an HTTP route definition
 *
 * Associates an HTTP request method and a validated route pattern with a handler responsible for producing an HTTP
 * response.
 *
 * A route handler may be a closure or the class name of an invokable controller.
 *
 * <code>
 * $route = new Route(
 *     Standard::GET,
 *     new Pattern('/users/{id}'),
 *     UserController::class
 * );
 *
 * $route->method();
 * $route->pattern();
 * $route->handler();
 * </code>
 *
 * @since 1.0.0
 *
 * @phpstan-type RouteHandler (
 *     Closure(
 *         \FireHub\Adapter\HTTP\Router\ActionContext
 *     ):\FireHub\Adapter\HTTP\Response
 *     | class-string<\FireHub\Adapter\HTTP\Controller>
 *     | array{class-string<\FireHub\Adapter\HTTP\Controller>, non-empty-string}
 * )
 */
final readonly class Route {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Pattern $pattern <p>
     * Validated route pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Method $method,
        private Pattern $pattern,
        private Closure|string|array $handler
    ) {}

    /**
     * ### Gets the HTTP request method
     *
     * <code>
     * $route->method();
     *
     * // Standard::GET
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request\Method HTTP request method.
     */
    public function method ():Method {

        return $this->method;

    }

    /**
     * ### Gets the route pattern
     *
     * <code>
     * $route->pattern()->value();
     *
     * // '/users'
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Router\Pattern Route pattern.
     */
    public function pattern ():Pattern {

        return $this->pattern;

    }

    /**
     * ### Gets the route handler
     *
     * <code>
     * $route->handler();
     *
     * // UserController::class or Closure
     * </code>
     *
     * @since 1.0.0
     *
     * @return RouteHandler Route handler.
     */
    public function handler ():Closure|string|array {

        return $this->handler;

    }

}