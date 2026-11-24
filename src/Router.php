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

namespace FireHub\Adapter\HTTP;

use FireHub\Adapter\HTTP\Request\ {
    Method\Standard as RequestMethod, Target\Absolute, Target\Origin,
    Method
};
use FireHub\Adapter\HTTP\Response\ {
    Status\Standard as ResponseStatus,
    Headers, Resolver
};
use FireHub\Adapter\HTTP\Header\Allow;
use FireHub\Adapter\HTTP\Router\ {
    Matcher\AllowedMethods, Route\Name, Storage\Map,
    Dispatcher, Matcher, Route, RouteBuilder, Storage
};
use Closure;

/**
 * ### Manages HTTP route registration and dispatching
 *
 * Provides the primary entry point for defining and executing HTTP routes.
 *
 * Associates HTTP request methods and URI path patterns with route handlers, coordinates route matching, and
 * delegates handler execution to the dispatcher.
 *
 * Route definitions are stored through a configurable storage implementation, allowing different indexing strategies
 * without changing routing behavior.
 *
 * When no storage implementation is provided, the router uses the default map-based storage.
 *
 * The router handles unmatched routes, unsupported request methods, and automatic OPTIONS responses according to
 * HTTP routing semantics.
 *
 * The router does not construct controllers, resolve action dependencies, manage application lifecycles, or emit
 * HTTP responses.
 *
 * <code>
 * $router = new Router();
 *
 * $router->get('/users', [UserController::class, 'index']);
 *
 * $router->get('/users/{id}', [UserController::class, 'show']);
 *
 * $router->post('/users', [UserController::class, 'store']);
 *
 * $response = $router->dispatch($context);
 * </code>
 *
 * @since 1.0.0
 *
 * @phpstan-import-type RouteHandler from \FireHub\Adapter\HTTP\Router\Route
 */
final class Router implements Handler {

    /**
     * ### Route storage
     * @since 1.0.0
     */
    private Storage $storage;

    /**
     * ### Route matcher
     * @since 1.0.0
     */
    private Matcher $matcher;

    /**
     * ### Route dispatcher
     * @since 1.0.0
     */
    private Dispatcher $dispatcher;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Resolver::default() To create the default HTTP response resolver.
     *
     * @param null|\FireHub\Adapter\HTTP\Router\Storage $storage [optional] <p>
     * Route storage implementation, or null to use the default map-based storage.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Resolver $resolver [optional] <p>
     * HTTP response resolver, or null to use the default value resolution strategies.
     * </p>
     *
     * @return void
     */
    public function __construct (?Storage $storage = null, ?Resolver $resolver = null) {

        $this->storage = $storage ?? new Map;

        $this->matcher = new Matcher($this->storage);

        $this->dispatcher = new Dispatcher(
            $resolver ?? Resolver::default()
        );

    }

    /**
     * ### Registers an HTTP route
     *
     * Associates an HTTP request method and URI path pattern with a handler.
     *
     * The handler may be a closure, an invokable controller class, or a controller class and action method pair.
     *
     * <code>
     * $router->add(
     *     RequestMethod::GET,
     *     '/users/{id}',
     *     UserController::class,
     *     static function (RouteBuilder $route):void {
     *
     *         $route
     *             ->name('users.show')
     *             ->middleware(AuthenticationMiddleware::class)
     *
     *     }
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::build() To build the route.
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     *
     * @phpstan-import-type RouteHandler from Route
     */
    public function add (Method $method, string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $builder = new RouteBuilder(
            $method,
            $pattern,
            $handler
        );

        $configure?->__invoke($builder);

        $this->storage->add(
            $builder->build()
        );

    }

    /**
     * ### Registers a GET route
     *
     * <code>
     * $router->get('/users', [UserController::class, 'index']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function get (string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $this->add(RequestMethod::GET, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a POST route
     *
     * <code>
     * $router->post('/users', [UserController::class, 'store']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function post (string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $this->add(RequestMethod::POST, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a PUT route
     *
     * <code>
     * $router->put('/users/{id}', [UserController::class, 'update']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function put (string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $this->add(RequestMethod::PUT, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a PATCH route
     *
     * <code>
     * $router->patch('/users/{id}', [UserController::class, 'patch']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function patch (string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $this->add(RequestMethod::PATCH, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a DELETE route
     *
     * <code>
     * $router->delete('/users/{id}', [UserController::class, 'delete']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function delete (string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $this->add(RequestMethod::DELETE, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a HEAD route
     *
     * <code>
     * $router->head('/users', [UserController::class, 'head']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function head (string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $this->add(RequestMethod::HEAD, $pattern, $handler, $configure);

    }

    /**
     * ### Registers an OPTIONS route
     *
     * Explicit OPTIONS handlers take precedence over automatic OPTIONS responses generated by the router.
     *
     * <code>
     * $router->options('/users', [UserController::class, 'options']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * URI path pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     * @param null|Closure(\FireHub\Adapter\HTTP\Router\RouteBuilder):void $configure <p>
     * Optional route configuration callback.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function options (string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $this->add(RequestMethod::OPTIONS, $pattern, $handler, $configure);

    }

    /**
     * ### Handles an HTTP request
     *
     * Processes an HTTP request by dispatching it to the matching route.
     *
     * Delegates route matching, handler execution, and automatic HTTP error responses to the router dispatcher.
     *
     * <code>
     * $response = $router->handle($context);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router::dispatch() To dispatch the request.
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function handle (ExecutionContext $context):Response {

        return $this->dispatch($context);

    }

    /**
     * ### Dispatches an HTTP request
     *
     * Matches the current HTTP request against registered routes and executes the corresponding handler.
     *
     * Returns 404 when no route pattern matches the request path.
     *
     * Returns 405 with an Allow header when the path exists, but the requested HTTP method is not supported.
     *
     * Automatically handles OPTIONS requests when no explicit OPTIONS handler is registered for the matching route
     * pattern.
     *
     * <code>
     * $response = $router->dispatch($context);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::match() To match the request.
     * @uses \FireHub\Adapter\HTTP\Router\Matcher::allowedMethods() To retrieve supported methods.
     * @uses \FireHub\Adapter\HTTP\Router\Dispatcher::dispatch() To execute the route handler.
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function dispatch (ExecutionContext $context):Response {

        $request = $context->request();

        $target = $request->target();

        if ($target instanceof Origin) {

            $path = $target->path();

        } else if ($target instanceof Absolute) {

            $path = $target->uri()->path();

        } else {

            return new Response(
                ResponseStatus::NOT_IMPLEMENTED
            );

        }

        $method = $request->method();

        $match = $this->matcher->match(
            $method,
            $path
        );

        if ($match !== null)
            return $this->dispatcher->dispatch(
                $match,
                $context
            );

        $methods = $this->matcher->allowedMethods($path);

        if ($methods === [])
            return new Response(
                ResponseStatus::NOT_FOUND
            );

        $allowed = new AllowedMethods(...$methods);

        $headers = Headers::empty()->withAllow(
            new Allow(...$allowed->methods())
        );

        if ($method->value() === RequestMethod::OPTIONS->value)
            return new Response(
                ResponseStatus::NO_CONTENT,
                headers: $headers
            );

        return new Response(
            ResponseStatus::METHOD_NOT_ALLOWED,
            headers: $headers
        );

    }

    /**
     * ### Finds a registered route by name
     *
     * Retrieves a route definition using its unique name.
     *
     * Returns null when no route with the specified name exists.
     *
     * <code>
     * $route = $router->route(
     *     new Name('users.show')
     * );
     *
     * if ($route !== null)
     *     echo $route->pattern()->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::findByName() To retrieve the registered route.
     *
     * @param \FireHub\Adapter\HTTP\Router\Route\Name $name <p>
     * Registered route name.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Router\Route Matching route, or null if not found.
     */
    public function route (Name $name):?Route {

        return $this->storage->findByName($name);

    }

    /**
     * ### Freezes route registration
     *
     * Prevents further modifications to the underlying route storage.
     *
     * <code>
     * $router->freeze();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::freeze() To freeze the route storage.
     *
     * @return void
     */
    public function freeze ():void {

        $this->storage->freeze();

    }

    /**
     * ### Checks whether route registration is frozen
     *
     * <code>
     * if ($router->isFrozen()) {
     *     // Route registration is closed.
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Storage::isFrozen() To check if route storage is frozen.
     *
     * @return bool True if route storage is frozen.
     */
    public function isFrozen ():bool {

        return $this->storage->isFrozen();

    }

}