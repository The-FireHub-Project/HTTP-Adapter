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

use FireHub\Adapter\HTTP\Middleware;
use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Adapter\HTTP\Router\Exception\ {
    InvalidRouteNameException, RouteBuilderAlreadyBuiltException
};
use Closure;

/**
 * ### Builds immutable HTTP route definitions
 *
 * Provides a mutable configuration stage for HTTP routes before they are registered in route storage.
 *
 * Supports route names and route-specific middleware.
 *
 * Once built, the builder cannot be modified or built again.
 *
 * <code>
 * $builder = new RouteBuilder(
 *     Standard::GET,
 *     '/users/{id}',
 *     UserController::class
 * );
 *
 * $route = $builder
 *     ->name('users.show')
 *     ->middleware(new AuthenticationMiddleware())
 *     ->build();
 * </code>
 *
 * @since 1.0.0
 *
 * @phpstan-import-type RouteHandler from Route
 */
final class RouteBuilder {

    /**
     * ### Route name
     * @since 1.0.0
     *
     * @var null|non-empty-string
     */
    private ?string $name = null;

    /**
     * ### Route middleware
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Middleware>
     */
    private array $middleware = [];

    /**
     * ### Builder completion state
     * @since 1.0.0
     */
    private bool $built = false;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     * @param non-empty-string $pattern <p>
     * URI route pattern.
     * </p>
     * @param RouteHandler $handler <p>
     * Route handler.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private readonly Method $method,
        private readonly string $pattern,
        private readonly Closure|string|array $handler
    ) {}

    /**
     * ### Sets the route name
     *
     * Assigns a unique identifier that can later be used to retrieve the route or generate its URI.
     *
     * <code>
     * $builder->name('users.show');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::assertConfigurable() To ensure the builder is not already built.
     *
     * @param non-empty-string $name <p>
     * Route name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteNameException If the route name is empty.
     *
     * @return self Current builder.
     */
    public function name (string $name):self {

        $this->assertConfigurable();

        if ($name === '')
            throw new InvalidRouteNameException(
                'Route name cannot be empty.'
            );

        $this->name = $name;

        return $this;

    }

    /**
     * ### Appends route middleware
     *
     * Adds middleware components in their execution order.
     *
     * Multiple calls append middleware without replacing previously registered components.
     *
     * <code>
     * $builder
     *     ->middleware(new AuthenticationMiddleware())
     *     ->middleware(new AuthorizationMiddleware());
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::assertConfigurable() To ensure the builder is not already built.
     *
     * @param \FireHub\Adapter\HTTP\Middleware...$middleware <p>
     * Middleware components to append.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     *
     * @return self Current builder.
     */
    public function middleware (Middleware ...$middleware):self {

        $this->assertConfigurable();

        foreach ($middleware as $component)
            $this->middleware[] = $component;

        return $this;

    }

    /**
     * ### Builds the immutable route definition
     *
     * Creates a route from the current configuration and prevents further modifications to this builder.
     *
     * <code>
     * $route = $builder->build();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::assertConfigurable() To ensure the builder is not already built.
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     *
     * @return \FireHub\Adapter\HTTP\Router\Route Immutable route definition.
     *
     * @internal
     */
    public function build ():Route {

        $this->assertConfigurable();

        $route = new Route(
            $this->method,
            new Pattern($this->pattern),
            $this->handler,
            $this->name,
            ...$this->middleware
        );

        $this->built = true;

        return $route;

    }

    /**
     * ### Ensures the builder remains configurable
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     *
     * @return void
     */
    private function assertConfigurable ():void {

        if ($this->built)
            throw new RouteBuilderAlreadyBuiltException;

    }

}