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

use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Adapter\HTTP\Router\ {
    Constraint\Integer, Constraint\Number, Route\Name
};
use FireHub\Adapter\HTTP\Router\Exception\ {
    InvalidRouteConstraintException, RouteBuilderAlreadyBuiltException
};
use FireHub\Runtime;
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
 *     ->name(new Name('users.show'))
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
     */
    private ?Name $name = null;

    /**
     * ### Route parameter constraints
     * @since 1.0.0
     *
     * @var array<non-empty-string, \FireHub\Adapter\HTTP\Router\Constraint>
     */
    private array $constraints = [];

    /**
     * ### Route middleware
     * @since 1.0.0
     *
     * @var list<class-string<\FireHub\Adapter\HTTP\Middleware>>
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
     * $builder->name(new Name('users.show'));
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::assertConfigurable() To ensure the builder is not already built.
     *
     * @param \FireHub\Adapter\HTTP\Router\Route\Name $name <p>
     * Route name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     *
     * @return self Current builder.
     */
    public function name (Name $name):self {

        $this->assertConfigurable();

        $this->name = $name;

        return $this;

    }

    /**
     * ### Adds a route parameter constraint
     *
     * Associates a constraint with a named route parameter.
     *
     * <code>
     * $builder->where(
     *     'id',
     *     new Number()
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::assertConfigurable() To ensure the builder is not already built.
     *
     * @param non-empty-string $parameter <p>
     * Route parameter name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Constraint $constraint <p>
     * Parameter constraint.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     *
     * @return self Current builder.
     */
    public function where (string $parameter, Constraint $constraint):self {

        $this->assertConfigurable();

        $this->constraints[$parameter] = $constraint;

        return $this;

    }

    /**
     * ### Restricts a route parameter to decimal digits
     *
     * <code>
     * $builder->whereNumber('id');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::where() To add the constraint.
     *
     * @param non-empty-string $parameter <p>
     * Route parameter name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     *
     * @return self Current builder.
     */
    public function whereNumber (string $parameter):self {

        return $this->where(
            $parameter,
            new Number
        );

    }

    /**
     * ### Restricts a route parameter to a PHP integer
     *
     * Validates the parameter and converts it into an integer when the route is matched.
     *
     * <code>
     * $builder->whereInteger('id');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::where()
     * To register the integer constraint.
     *
     * @param non-empty-string $parameter Route parameter name.
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException
     * If the builder has already been built.
     *
     * @return self Current builder.
     */
    public function whereInteger (string $parameter):self {

        return $this->where(
            $parameter,
            new Integer
        );

    }

    /**
     * ### Appends route middleware
     *
     * Adds middleware class references in their execution order.
     *
     * Middleware is resolved through the current HTTP execution dependency injection scope during request processing.
     *
     * Multiple calls append middleware without replacing previously registered components.
     *
     * <code>
     * $builder
     *     ->middleware(AuthenticationMiddleware::class)
     *     ->middleware(AuthorizationMiddleware::class);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::assertConfigurable() To ensure the builder is not already built.
     *
     * @param class-string<\FireHub\Adapter\HTTP\Middleware> ...$middleware <p>
     * Middleware class references to append.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     *
     * @return self Current builder.
     */
    public function middleware (string ...$middleware):self {

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
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteConstraintException If a route parameter constraint
     * is invalid.
     *
     * @return \FireHub\Adapter\HTTP\Router\Route Immutable route definition.
     *
     * @internal
     */
    public function build ():Route {

        $this->assertConfigurable();

        $pattern = new Pattern($this->pattern);

        foreach ($this->constraints as $parameter => $constraint)
            if (!Runtime\Arr\Inspection::inArray(
                $pattern->parameters(),
                $parameter
            )) throw new InvalidRouteConstraintException(
                "Route parameter '$parameter' does not exist."
            );

        $route = new Route(
            $this->method,
            $pattern,
            $this->handler,
            $this->name,
            $this->constraints,
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