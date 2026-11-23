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
    ExecutionContext, Response
};
use FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException;
use FireHub\Runtime;
use Closure;

/**
 * ### Dispatches matched HTTP routes
 *
 * Executes the handler associated with a matched HTTP route.
 *
 * Closure handlers are invoked directly.
 *
 * Controller handlers are resolved through the current HTTP execution dependency injection scope.
 *
 * The dispatcher does not perform route matching, HTTP method validation, or HTTP response emission.
 *
 * <code>
 * $dispatcher = new Dispatcher();
 *
 * $response = $dispatcher->dispatch(
 *     $match,
 *     $context
 * );
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Dispatcher {

    /**
     * ### Dispatches a matched HTTP route
     *
     * Executes the route handler and returns its HTTP response.
     *
     * Controller handlers must be registered in the current dependency injection scope and implement an invokable
     * method.
     *
     * <code>
     * $response = $dispatcher->dispatch(
     *     $match,
     *     $context
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::route() To retrieve the matched route.
     * @uses \FireHub\Adapter\HTTP\Router\Route::handler() To retrieve the route handler.
     * @uses \FireHub\Adapter\HTTP\ExecutionContext::scope() To retrieve the execution dependency injection scope.
     * @uses \FireHub\Core\Boundary\Resolver::get() To resolve a registered controller.
     * @uses \FireHub\Runtime\DataIs::callable() To check if the resolved controller is callable.
     *
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched HTTP route.
     * </p>
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException If the resolved controller is not
     * callable.
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function dispatch (RouteMatch $match, ExecutionContext $context):Response {

        $handler = $match->route()->handler();

        if ($handler instanceof Closure)
            return $handler($context, $match);

        $controller = $context->scope()->get($handler);

        if (!Runtime\DataIs::callable($controller))
            throw new InvalidRouteHandlerException(
                'The resolved route controller must be invokable.'
            );

        $response = $controller($context, $match);

        if (!$response instanceof Response)
            throw new InvalidRouteHandlerException(
                'The route controller must return an HTTP response.'
            );

        return $response;

    }

}