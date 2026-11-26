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
 * ### Declares a common route path prefix for a controller
 *
 * Associates a controller class with a common route path prefix that applies to all HTTP routes declared
 * on its controller actions.
 *
 * The prefix may contain static path segments and route parameters.
 *
 * This attribute provides declarative routing metadata without performing route path composition,
 * validation, or registration.
 *
 * <code>
 * #[RoutePrefix('/users')]
 * final class UserController implements Controller {
 *
 *     #[Get('/')]
 *     public function index ():Response {
 *
 *         // Controller action implementation.
 *
 *     }
 *
 *     #[Get('/{id}')]
 *     public function show (int $id):Response {
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
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class RoutePrefix {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param non-empty-string $prefix <p>
     * Common route path prefix applied to the controller actions.
     * </p>
     *
     * @return void
     */
    public function __construct (private string $prefix) {}

    /**
     * ### Gets the common route path prefix
     *
     * <code>
     * $prefix = new RoutePrefix('/api/v1');
     *
     * $prefix->prefix(); // '/api/v1'
     * </code>
     *
     * @since 1.0.0
     *
     * @return string Common route path prefix.
     */
    public function prefix ():string {

        return $this->prefix;

    }

}