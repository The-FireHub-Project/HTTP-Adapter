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

use FireHub\Adapter\HTTP\ {
    ExecutionContext, Request
};

/**
 * ### Represents an HTTP route action execution context
 *
 * Provides a unified, read-only context for executing HTTP route handlers and controller actions.
 *
 * Combines the current HTTP execution context with the matched route, allowing handlers to access the incoming
 * request, route definition, and extracted route parameters through a single object.
 *
 * The action context does not perform route matching, resolve dependencies, invoke handlers, or manage the
 * application lifecycle. These responsibilities remain with the router, dispatcher, dependency injection container,
 * and application respectively.
 *
 * Each action context is associated with a single HTTP request execution and its corresponding route match. It does
 * not own or manage the underlying execution context or dependency injection scope.
 *
 * <code>
 * $router->get(
 *     '/users/{id}',
 *     function (ActionContext $context):Response {
 *
 *         $request = $context->request();
 *
 *         $route = $context->route();
 *
 *         $id = $context->parameter('id');
 *
 *         return new Response(Standard::OK);
 *
 *     }
 * );
 * </code>
 *
 * @since 1.0.0
 */
final readonly class ActionContext {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $execution <p>
     * HTTP execution context.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched HTTP route.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private ExecutionContext $execution,
        private RouteMatch $match
    ) {}

    /**
     * ### Gets the HTTP request
     *
     * <code>
     * $context->request();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\ExecutionContext::request() To retrieve the request.
     *
     * @return \FireHub\Adapter\HTTP\Request HTTP request.
     */
    public function request ():Request {

        return $this->execution->request();

    }

    /**
     * ### Gets the matched route
     *
     * <code>
     * $context->route();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::route() To retrieve the matched route.
     *
     * @return \FireHub\Adapter\HTTP\Router\Route Matched route.
     */
    public function route ():Route {

        return $this->match->route();

    }

    /**
     * ### Gets all matched route parameters
     *
     * <code>
     * $parameters = $context->parameters();
     *
     * // ['id' => 123, 'test' => '20']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::parameters() To retrieve matched parameters.
     *
     * @return array<non-empty-string, scalar> Matched route parameters.
     */
    public function parameters ():array {

        return $this->match->parameters();

    }

    /**
     * ### Gets a named route parameter
     *
     * Returns the parameter in the PHP type defined by its constraint.
     *
     * <code>
     * $id = $context->parameter('id');
     *
     * // int(123), when using whereInteger('id')
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::parameter() To retrieve the parameter value.
     *
     * @param non-empty-string $name <p>
     * Parameter name.
     * </p>
     *
     * @return null|scalar Converted parameter value.
     */
    public function parameter (string $name):null|int|float|string|bool {

        return $this->match->parameter($name);

    }

    /**
     * ### Gets the HTTP execution context
     *
     * <code>
     * $context->execution();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\ExecutionContext HTTP execution context.
     */
    public function execution ():ExecutionContext {

        return $this->execution;

    }

}