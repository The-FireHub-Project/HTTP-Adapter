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

namespace FireHub\Adapter\HTTP\Router\Attribute;

use Attribute;

/**
 * ### Declares middleware for a controller or controller action
 *
 * Associates an HTTP middleware class with a controller class or an individual controller action.
 *
 * Middleware declared on a controller class applies to all its registered routes, while middleware declared
 * on a controller action applies only to the routes associated with that action.
 *
 * Multiple middleware attributes may be declared on the same controller class or controller action.
 *
 * This attribute provides declarative routing metadata without performing middleware instantiation,
 * registration, or execution.
 *
 * <code>
 * #[Middleware(AuthenticationMiddleware::class)]
 * final class UserController implements Controller {
 *
 *     #[Get('/users')]
 *     #[Middleware(AuthorizationMiddleware::class)]
 *     public function index ():Response {
 *
 *         // Controller action implementation.
 *
 *     }
 *
 * }
 * </code>
 *
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Middleware {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param class-string<\FireHub\Adapter\HTTP\Middleware> $middleware <p>
     * HTTP middleware class associated with the controller or controller action.
     * </p>
     *
     * @return void
     */
    public function __construct (private string $middleware) {}

    /**
     * ### Gets the HTTP middleware class
     *
     * <code>
     * $middleware = new Middleware(AuthenticationMiddleware::class);
     *
     * $middleware->middleware(); // AuthenticationMiddleware::class
     * </code>
     *
     * @since 1.0.0
     *
     * @return class-string<\FireHub\Adapter\HTTP\Middleware> HTTP middleware class.
     */
    public function middleware ():string {

        return $this->middleware;

    }

}