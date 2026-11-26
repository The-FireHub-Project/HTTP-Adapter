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

namespace FireHub\Adapter\HTTP\Router\Attribute;

use FireHub\Adapter\HTTP\Request\Method;
use Attribute;

/**
 * ### Declares an HTTP route on a controller action
 *
 * Associates a controller action with an HTTP request method, a route path pattern, and an optional route name.
 *
 * Multiple route attributes may be declared on the same controller action, allowing a single action to handle
 * different HTTP request methods or route path patterns.
 *
 * This attribute provides declarative routing metadata without performing route registration, route matching,
 * or controller execution.
 *
 * <code>
 * #[Route(Method\Standard::GET, '/users', 'users.index')]
 * public function index ():Response {
 *
 *     // Controller action implementation.
 *
 * }
 * </code>
 *
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Route {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method associated with the controller action.
     * </p>
     * @param non-empty-string $pattern <p>
     * HTTP route path pattern.
     * </p>
     * @param null|string $name [optional] <p>
     * Unique route name, or null if the route is unnamed.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Method $method,
        private string $pattern,
        private ?string $name = null
    ) {}

    /**
     * ### Gets the HTTP request method
     *
     * <code>
     * $route->method(); // Method\Standard::GET
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
     * ### Gets the HTTP route path pattern
     *
     * <code>
     * $route->pattern(); // '/users/{id}'
     * </code>
     *
     * @since 1.0.0
     *
     * @return string HTTP route path pattern.
     */
    public function pattern ():string {

        return $this->pattern;

    }

    /**
     * ### Gets the HTTP route name
     *
     * <code>
     * $route->name(); // 'users.show'
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|string HTTP route name, or null if the route is unnamed.
     */
    public function name ():?string {

        return $this->name;

    }

}