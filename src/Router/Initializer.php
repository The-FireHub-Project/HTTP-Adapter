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
    Adapter, Initializer as AdapterInitializer, Router
};
use FireHub\Runtime;

/**
 * ### Initializes HTTP routing during the adapter startup lifecycle
 *
 * Registers routes declared on controller classes and freezes the router before the HTTP adapter becomes
 * operational.
 *
 * Supports both manually registered routes and routes declared through controller attributes.
 *
 * Controller route discovery is performed during initialization rather than during HTTP request execution.
 *
 * <code>
 * $router = new Router;
 *
 * $adapter->registerInitializer(
 *     new Initializer(
 *         $router,
 *         UserController::class,
 *         ProductController::class
 *     )
 * );
 *
 * $adapter->initialize();
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Initializer implements AdapterInitializer {

    /**
     * ### Controller classes
     * @since 1.0.0
     *
     * @var list<class-string<\FireHub\Adapter\HTTP\Controller>>
     */
    private array $controllers;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To preserve insertion order.
     *
     * @param \FireHub\Adapter\HTTP\Router $router <p>
     * Router to initialize.
     * </p>
     * @param class-string<\FireHub\Adapter\HTTP\Controller> ...$controllers <p>
     * Controller classes whose declared routes should be registered during initialization.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Router $router,
        string ...$controllers
    ) {

        $this->controllers = Runtime\Arr\Access::values($controllers);

    }

    /**
     * ### Initializes HTTP routing
     *
     * Registers routes declared on the configured controller classes and freezes the underlying route storage.
     *
     * If controller route registration fails, the exception is propagated to the HTTP adapter lifecycle.
     *
     * <code>
     * $adapter->registerInitializer($initializer);
     *
     * $adapter->initialize();
     *
     * $router->isFrozen(); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\ControllerRegistrar::register() To register routes declared on controller
     * classes.
     * @uses \FireHub\Adapter\HTTP\Router::freeze() To prevent further route registration.
     *
     * @param \FireHub\Adapter\HTTP\Adapter $adapter <p>
     * HTTP adapter being initialized.
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
    public function initialize (Adapter $adapter):void {

        $registrar = new ControllerRegistrar($this->router);

        $registrar->register(...$this->controllers);

        $this->router->freeze();

    }

}