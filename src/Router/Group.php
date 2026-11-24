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

use FireHub\Adapter\HTTP\Router;
use FireHub\Adapter\HTTP\Request\ {
    Method, Method\Standard
};
use FireHub\Runtime;
use Closure;

/**
 * ### Groups HTTP routes under a common path prefix
 *
 * Provides a registration context for HTTP routes that share a path prefix and optional middleware components.
 *
 * Nested groups inherit the path prefix and middleware registered on their parent group at the time the nested group
 * is created.
 *
 * Middleware is copied into each route during registration. Subsequent changes to the group do not affect previously
 * registered routes.
 *
 * Groups do not participate in request matching or execution.
 *
 * <code>
 * $router->group('/api', function (Group $group):void {
 *
 *     $group->middleware(new AuthenticationMiddleware());
 *
 *     $group->get('/users', [UserController::class, 'index']);
 *
 *     $group->group('/admin', function (Group $admin):void {
 *
 *         $admin->middleware(new AuthorizationMiddleware());
 *
 *         $admin->delete(
 *             '/users/{id}',
 *             [AdminController::class, 'delete']
 *         );
 *
 *     });
 *
 * });
 * </code>
 *
 * @since 1.0.0
 *
 * @phpstan-import-type RouteHandler from Route
 */
final class Group {

    /**
     * ### Registered group middleware
     * @since 1.0.0
     *
     * @var list<class-string<\FireHub\Adapter\HTTP\Middleware>>
     */
    private array $middleware;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To preserve insertion order.
     *
     * @param \FireHub\Adapter\HTTP\Router $router <p>
     * Router responsible for registering routes.
     * </p>
     * @param non-empty-string $prefix <p>
     * Absolute group path prefix.
     * </p>
     * @param class-string<\FireHub\Adapter\HTTP\Middleware> ...$middleware <p>
     * Middleware class references inherited from parent groups.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the prefix is invalid.
     *
     * @return void
     */
    public function __construct (
        private readonly Router $router,
        private readonly string $prefix,
        string ...$middleware
    ) {

        new Pattern($prefix);

        $this->middleware = Runtime\Arr\Access::values($middleware);

    }

    /**
     * ### Registers group middleware
     *
     * Appends middleware class references to the current group.
     *
     * Middleware applies only to routes and nested groups registered after this method is called.
     *
     * Middleware instances are resolved through the current HTTP execution dependency injection scope during request
     * processing.
     *
     * <code>
     * $group->middleware(
     *     AuthenticationMiddleware::class,
     *     AuthorizationMiddleware::class
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @param class-string<\FireHub\Adapter\HTTP\Middleware> ...$middleware <p>
     * Middleware class references to append.
     * </p>
     *
     * @return void
     */
    public function middleware (string ...$middleware):void {

        foreach ($middleware as $component)
            $this->middleware[] = $component;

    }

    /**
     * ### Registers a nested route group
     *
     * Creates a child group with the combined path prefix and a snapshot of the current group's middleware.
     *
     * <code>
     * $group->group('/admin', function (Group $admin):void {
     *
     *     $admin->get('/users', AdminController::class);
     *
     * });
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::combine() To combine the group prefix with the nested group path.
     *
     * @param non-empty-string $prefix <p>
     * Relative path prefix for the nested group.
     * </p>
     * @param Closure(self):void $callback <p>
     * Callback used to register routes within the nested group.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the prefix is invalid.
     *
     * @return void
     */
    public function group (string $prefix, Closure $callback):void {

        $group = new self(
            $this->router,
            $this->combine($prefix),
            ...$this->middleware
        );

        $callback($group);

    }

    /**
     * ### Registers an HTTP route within the group
     *
     * Combines the group prefix with the supplied route pattern and registers the resulting route with inherited and
     * route-specific middleware.
     *
     * <code>
     * $group->add(
     *     Standard::GET,
     *     '/users',
     *     UserController::class,
     *     static function (RouteBuilder $route):void {
     *         $route->name('users.index');
     *     }
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::combine() To combine the group prefix with the route path.
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::middleware() To apply inherited and route-specific middleware.
     * @uses \FireHub\Adapter\HTTP\Router::add() To register the route.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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
    public function add (Method $method, string $pattern, Closure|string|array $handler, ?Closure $configure = null):void {

        $middleware = $this->middleware;

        $this->router->add(
            $method,
            $this->combine($pattern),
            $handler,
            static function (RouteBuilder $builder) use (
                $middleware,
                $configure
            ):void {

                $builder->middleware(...$middleware);

                $configure?->__invoke($builder);

            }
        );

    }

    /**
     * ### Registers a GET route
     *
     * <code>
     * $group->get('/users', [UserController::class, 'index']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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

        $this->add(Standard::GET, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a POST route
     *
     * <code>
     * $group->post('/users', [UserController::class, 'store']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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

        $this->add(Standard::POST, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a PUT route
     *
     * <code>
     * $group->put('/users/{id}', [UserController::class, 'update']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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

        $this->add(Standard::PUT, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a PATCH route
     *
     * <code>
     * $group->patch('/users/{id}', [UserController::class, 'patch']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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

        $this->add(Standard::PATCH, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a DELETE route
     *
     * <code>
     * $group->delete('/users/{id}', [UserController::class, 'delete']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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

        $this->add(Standard::DELETE, $pattern, $handler, $configure);

    }

    /**
     * ### Registers a HEAD route
     *
     * <code>
     * $group->head('/users', [UserController::class, 'head']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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

        $this->add(Standard::HEAD, $pattern, $handler, $configure);

    }

    /**
     * ### Registers an OPTIONS route
     *
     * <code>
     * $group->options('/users', [UserController::class, 'options']);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Group::add() To register the route.
     *
     * @param non-empty-string $pattern <p>
     * Route path pattern relative to the group prefix.
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

        $this->add(Standard::OPTIONS, $pattern, $handler, $configure);

    }

    /**
     * ### Combines the group prefix with a relative route path
     *
     * Validates the relative path and returns a complete route pattern. The root path is treated as an empty suffix.
     * @since 1.0.0
     *
     * @param non-empty-string $path <p>
     * Relative route path.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the path is invalid.
     *
     * @return non-empty-string Combined route path.
     */
    private function combine (string $path):string {

        new Pattern($path);

        if ($path === '/')
            return $this->prefix;

        if ($this->prefix === '/')
            return $path;

        $combined = $this->prefix.$path;

        new Pattern($combined);

        return $combined;

    }

}