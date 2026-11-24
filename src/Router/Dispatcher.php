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

use FireHub\Adapter\HTTP\Middleware\Pipeline;
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
     * ### Dispatches a matched HTTP route
     *
     * Resolves controller instances through the current dependency injection scope and invokes the selected route
     * action.
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
     * @uses \FireHub\Adapter\HTTP\Router\Route::middleware() To retrieve the route middleware.
     * @uses \FireHub\Adapter\HTTP\Router\Dispatcher::execute() To execute the route handler.
     * @uses \FireHub\Adapter\HTTP\Middleware\Pipeline::handle() To execute the middleware pipeline.
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

        $middleware = $match->route()->middleware();

        if ($middleware === [])
            return $this->execute($match, $context);

        $pipeline = new Pipeline(
            fn (ExecutionContext $context):Response => $this->execute(
                $match,
                $context
            ),
            ...$middleware
        );

        return $pipeline->handle($context);

    }

    /**
     * ### Executes a matched route handler
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::route() To retrieve the matched route.
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::parameters() To retrieve the route parameters.
     * @uses \FireHub\Adapter\HTTP\Router\Route::handler() To retrieve the route handler.
     * @uses \FireHub\Adapter\HTTP\ExecutionContext::scope() To retrieve the current dependency injection scope.
     * @uses \FireHub\Runtime\DataIs::callable() To check if the resolved controller action is callable.
     * @uses \FireHub\Runtime\DataIs::string() To check if the route handler is a string.
     *
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched route.
     * </p>
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * HTTP execution context.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    private function execute (RouteMatch $match, ExecutionContext $context):Response {

        $handler = $match->route()->handler();

        $action_context = new ActionContext(
            $context,
            $match
        );

        if ($handler instanceof Closure)
            return $handler($action_context);

        if (Runtime\DataIs::string($handler)) {

            $class = $handler;
            $method = '__invoke';

        } else {

            [$class, $method] = $handler;

        }

        /** @var \FireHub\Container\Scope $scope */
        $scope = $context->scope();

        $controller = $scope->get($class);

        if (!$controller instanceof Controller)
            throw new InvalidRouteHandlerException(
                'The resolved controller must implement the Controller interface.'
            );

        $callable = [$controller, $method];

        if (!Runtime\DataIs::callable($callable))
            throw new InvalidRouteHandlerException(
                "The controller action '{$method}' is not callable."
            );

        $parameters = $match->parameters();

        if (isset($parameters['context']))
            throw new InvalidRouteHandlerException(
                "Route parameter 'context' conflicts with the reserved action context argument."
            );

        $response = $scope->invoke(
            $callable,
            [
                'context' => $action_context,
                ...$parameters
            ]
        );

        if (!$response instanceof Response)
            throw new InvalidRouteHandlerException(
                'The controller action must return an HTTP response.'
            );

        return $response;

    }

}