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
 * Executes the handler associated with a matched HTTP route and resolves its returned value into an HTTP response.
 *
 * Closure handlers are invoked directly.
 *
 * Controller handlers are resolved through the current HTTP execution dependency injection scope.
 *
 * Route middleware always receives a resolved HTTP response from the route execution.
 *
 * The dispatcher does not perform route matching, HTTP method validation, response serialization, or HTTP response
 * emission.
 *
 * <code>
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
     * Executes the matched route handler through its configured middleware pipeline.
     *
     * The route action result is resolved into an HTTP response before being returned through the middleware pipeline.
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
     * @uses self::execute() To execute and resolve the route handler.
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::route() To retrieve the matched route.
     * @uses \FireHub\Adapter\HTTP\Router\Route::middleware() To retrieve the route middleware.
     * @uses \FireHub\Adapter\HTTP\Middleware\Pipeline::handle() To execute the middleware pipeline.
     *
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched HTTP route.
     * </p>
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException If the resolved controller or its
     * action is invalid.
     *
     * @return \FireHub\Adapter\HTTP\Response Resolved HTTP response.
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
     * ### Executes and resolves a matched route handler
     *
     * Creates the route action execution context, invokes the matched handler, and resolves its returned value into
     * an HTTP response.
     * @since 1.0.0
     *
     * @uses self::invoke() To invoke the matched route handler.
     *
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched HTTP route.
     * </p>
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException If the route handler is invalid.
     *
     * @return \FireHub\Adapter\HTTP\Response Resolved HTTP response.
     */
    private function execute (RouteMatch $match, ExecutionContext $context):Response {

        $action_context = new ActionContext(
            $context,
            $match
        );

        $response = $this->invoke(
            $match,
            $context,
            $action_context
        );

        if (!$response instanceof Response)
            throw new InvalidRouteHandlerException(
                'The route handler must return an HTTP response.'
            );

        return $response;

    }

    /**
     * ### Invokes a matched route handler
     *
     * Closure handlers are invoked directly with the route action execution context.
     *
     * Controller handlers are resolved through the current dependency injection scope and invoked with the matched
     * route parameters and the route action execution context.
     *
     * The returned value is not required to be an HTTP response.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::route() To retrieve the matched route.
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::parameters() To retrieve the route parameters.
     * @uses \FireHub\Adapter\HTTP\Router\Route::handler() To retrieve the route handler.
     * @uses \FireHub\Adapter\HTTP\ExecutionContext::scope() To retrieve the current dependency injection scope.
     * @uses \FireHub\Runtime\DataIs::string() To determine whether the route handler is a controller class.
     * @uses \FireHub\Runtime\DataIs::callable() To determine whether the resolved controller action is callable.
     * @uses \FireHub\Container\Scope::get() To resolve the controller instance.
     * @uses \FireHub\Container\Scope::invoke() To invoke the resolved controller action.
     *
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched HTTP route.
     * </p>
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\ActionContext $action_context <p>
     * Current route action execution context.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException If the resolved controller is
     * invalid, its action is not callable, or a route parameter conflicts with a reserved argument.
     *
     * @return mixed Returned route action value.
     */
    private function invoke (RouteMatch $match, ExecutionContext $context, ActionContext $action_context):mixed {

        $handler = $match->route()->handler();

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
                "The controller action '$method' is not callable."
            );

        $parameters = $match->parameters();

        if (isset($parameters['context']))
            throw new InvalidRouteHandlerException(
                "Route parameter 'context' conflicts with the reserved action context argument."
            );

        return $scope->invoke(
            $callable,
            [
                'context' => $action_context,
                ...$parameters
            ]
        );

    }

}