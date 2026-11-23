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

namespace FireHub\Adapter\HTTP\Router;

use FireHub\Adapter\HTTP\ {
    Controller, ExecutionContext, Response
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
     * ### Constructor
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Router\ArgumentResolver $resolver <p>
     * Controller argument resolver.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private ArgumentResolver $resolver = new ArgumentResolver
    ) {}

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
     * @uses \FireHub\Adapter\HTTP\Router\ArgumentResolver::resolve() To resolve route action arguments.
     * @uses \FireHub\Runtime\DataIs::callable() To check if the resolved controller action is callable.
     * @uses \FireHub\Runtime\DataIs::string() To check if the route handler is a string.
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

        if (Runtime\DataIs::string($handler)) {

            $class = $handler;
            $method = '__invoke';

        } else {

            [$class, $method] = $handler;

        }

        $controller = $context->scope()->get($class);

        if (!$controller instanceof Controller)
            throw new InvalidRouteHandlerException(
                'The resolved route controller must implement the Controller interface.'
            );

        $callable = [$controller, $method];

        if (!Runtime\DataIs::callable($callable))
            throw new InvalidRouteHandlerException(
                "The specified controller action '{$method}' is not callable."
            );

        $arguments = $this->resolver->resolve(
            $controller,
            $method,
            $context,
            $match
        );

        $response = $callable(...$arguments);

        if (!$response instanceof Response)
            throw new InvalidRouteHandlerException(
                'The route controller action must return an HTTP response.'
            );

        return $response;

    }

}