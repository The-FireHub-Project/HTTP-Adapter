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

use FireHub\Core\Meta\Enum\Side;
use FireHub\Adapter\HTTP\ {
    Controller, Router
};
use FireHub\Adapter\HTTP\Router\Attribute\ {
    Constraint as ConstraintAttribute, Route as RouteAttribute, RoutePrefix, Middleware as MiddlewareAttribute
};
use FireHub\Adapter\HTTP\Router\Route\Name;
use FireHub\Adapter\HTTP\Router\Exception\ {
    InvalidRouteConstraintException, InvalidRouteHandlerException
};
use FireHub\Runtime;
use ReflectionAttribute, ReflectionClass, ReflectionMethod;

/**
 * ### Registers HTTP routes declared on controller classes
 *
 * Discovers declarative routing metadata through reflection and registers controller actions using the existing HTTP
 * routing system.
 *
 * Controller-level route prefixes and middleware are combined with action-level route metadata, middleware, and
 * parameter constraints.
 *
 * Controllers are not instantiated during route registration.
 *
 * <code>
 * $router = new Router;
 *
 * $registrar = new ControllerRegistrar($router);
 *
 * $registrar->register(UserController::class);
 * $registrar->register(ProductController::class);
 * </code>
 *
 * @since 1.0.0
 */
final readonly class ControllerRegistrar {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Router $router <p>
     * Router used to register controller routes.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Router $router
    ) {}

    /**
     * ### Registers routes declared on controller classes
     *
     * Discovers controller actions and registers their declared HTTP routes.
     *
     * <code>
     * $registrar->register(
     *     UserController::class,
     *     ProductController::class
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\ControllerRegistrar::registerController() To register individual controller
     * classes.
     *
     * @param class-string<\FireHub\Adapter\HTTP\Controller> ...$controllers <p>
     * Controller classes whose routes should be registered.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException If the controller action is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteConstraintException If multiple constraints are
     * declared for the same route parameter.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    public function register (string ...$controllers):void {

        foreach ($controllers as $controller)
            $this->registerController($controller);

    }

    /**
     * ### Registers routes declared on an individual controller
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\ControllerRegistrar::registerAction() To register individual controller
     * actions.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\RoutePrefix::prefix() To get the route path prefix.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Middleware::middleware() To get the middleware classes.
     *
     * @param class-string<\FireHub\Adapter\HTTP\Controller> $controller <p>
     * Controller class.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException If the controller action is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteConstraintException If multiple constraints are
     * declared for the same route parameter.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    private function registerController (string $controller):void {

        $reflection = new ReflectionClass($controller);

        if (!$reflection->isInstantiable() || !$reflection->implementsInterface(Controller::class))
            throw new InvalidRouteHandlerException(
                "Controller '$controller' must be an instantiable controller class."
            );

        $prefix = $this->prefix($reflection);
        $middleware = $this->middleware($reflection);

        foreach ($reflection->getMethods() as $method)
            $this->registerAction(
                $controller,
                $method,
                $prefix,
                $middleware
            );

    }

    /**
     * ### Registers routes declared on a controller action
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\ControllerRegistrar::registerAction() To register individual controller
     * actions.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Route::method() To get the HTTP request method.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Route::pattern() To get the route path pattern.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Route::name() To get the route name.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Route::middleware() To get the middleware classes.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Constraint::parameter() To get the parameter name.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Constraint::constraint() To get the constraint.
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::name() To set the route name.
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::middleware() To set the middleware classes.
     * @uses \FireHub\Adapter\HTTP\Router\RouteBuilder::where() To set the parameter constraints.
     *
     * @param class-string<\FireHub\Adapter\HTTP\Controller> $controller <p>
     * Controller class.
     * </p>
     * @param ReflectionMethod $method <p>
     * Reflected controller method.
     * </p>
     * @param null|non-empty-string $prefix <p>
     * Controller route path prefix.
     * </p>
     * @param list<class-string<\FireHub\Adapter\HTTP\Middleware>> $middleware <p>
     * Controller middleware.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteHandlerException If the controller action is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteConstraintException If multiple constraints are
     * declared for the same route parameter.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\RouteBuilderAlreadyBuiltException If the builder has already
     * been built.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRoutePatternException If the route path pattern is invalid.
     * @throws \FireHub\Adapter\HTTP\Router\Exception\DuplicateRouteException If an equivalent route is already
     * registered.
     *
     * @return void
     */
    private function registerAction (string $controller, ReflectionMethod $method, ?string $prefix, array $middleware):void {

        $routes = $method->getAttributes(
            RouteAttribute::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        if ($routes === []) return;

        if (
            !$method->isPublic() || $method->isStatic() || $method->isAbstract() || $method->isConstructor()
            || $method->isDestructor()
        ) throw new InvalidRouteHandlerException(
            "Controller action '{$method->getName()}' must be a public instance method."
        );

        $middleware = [
            ...$middleware,
            ...$this->middleware($method)
        ];

        $constraints = $this->constraints($method);

        foreach ($routes as $attribute) {

            $route = $attribute->newInstance();

            $this->router->add(
                $route->method(),
                $this->pattern($prefix, $route->pattern()),
                [$controller, $method->getName()],
                static function (RouteBuilder $builder) use ($route, $middleware, $constraints):void {

                    $name = $route->name();

                    if ($name !== null)
                        $builder->name(new Name($name));

                    $builder->middleware(...$middleware);

                    foreach ($constraints as $constraint)
                        $builder->where(
                            $constraint->parameter(),
                            $constraint->constraint()
                        );

                }
            );

        }

    }

    /**
     * ### Gets the route path prefix declared on a controller
     * @since 1.0.0
     *
     * @param ReflectionClass<\FireHub\Adapter\HTTP\Controller> $controller <p>
     * Reflected controller class.
     * </p>
     *
     * @return null|non-empty-string Controller route path prefix.
     */
    private function prefix (ReflectionClass $controller):?string {

        $attributes = $controller->getAttributes(RoutePrefix::class);

        if ($attributes === []) return null;

        return $attributes[0]->newInstance()->prefix();

    }

    /**
     * ### Gets middleware declared on a controller or controller action
     * @since 1.0.0
     *
     * @param ReflectionClass<\FireHub\Adapter\HTTP\Controller>|ReflectionMethod $reflection <p>
     * Reflected controller class or controller action.
     * </p>
     *
     * @return list<class-string<\FireHub\Adapter\HTTP\Middleware>> Declared middleware classes.
     */
    private function middleware (ReflectionClass|ReflectionMethod $reflection):array {

        $middleware = [];

        foreach ($reflection->getAttributes(MiddlewareAttribute::class) as $attribute)
            $middleware[] = $attribute->newInstance()->middleware();

        return $middleware;

    }

    /**
     * ### Gets parameter constraints declared on a controller action
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Constraint::parameter() To get the parameter name.
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Constraint::constraint() To get the constraint.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate constraints.
     *
     * @param ReflectionMethod $method <p>
     * Reflected controller action.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteConstraintException If multiple constraints are
     * declared for the same route parameter.
     *
     * @return list<\FireHub\Adapter\HTTP\Router\Attribute\Constraint> Declared parameter constraints.
     */
    private function constraints (ReflectionMethod $method):array {

        $constraints = [];

        foreach ($method->getAttributes(ConstraintAttribute::class) as $attribute) {

            $constraint = $attribute->newInstance();
            $parameter = $constraint->parameter();

            if (isset($constraints[$parameter]))
                throw new InvalidRouteConstraintException(
                    "Multiple constraints are declared for route parameter '$parameter'."
                );

            $constraints[$parameter] = $constraint;

        }

        return Runtime\Arr\Access::values($constraints);

    }

    /**
     * ### Combines a controller route prefix with an action route pattern
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim route path segments.
     *
     * @param null|non-empty-string $prefix <p>
     * Controller route path prefix.
     * </p>
     * @param non-empty-string $pattern <p>
     * Action route path pattern.
     * </p>
     *
     * @return non-empty-string Combined route path pattern.
     */
    private function pattern (?string $prefix, string $pattern):string {

        if ($prefix === null) return $pattern;

        $pattern = Runtime\Str\SB\Transform::trim($prefix, Side::RIGHT, '/')
            .'/'
            .Runtime\Str\SB\Transform::trim($pattern, Side::LEFT, '/');

        return Runtime\Str\SB\Transform::trim($pattern, Side::RIGHT, '/') ?: '/';

    }

}