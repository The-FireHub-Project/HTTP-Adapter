<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP;

use FireHub\Adapter\HTTP\Router\RouteMatch;

/**
 * ### Defines an HTTP controller
 *
 * Represents an invokable HTTP request handler responsible for processing a matched route and producing an HTTP
 * response.
 *
 * Controllers are resolved through the current execution dependency injection scope.
 *
 * <code>
 * final readonly class UserController implements Controller {
 *
 *     public function __invoke (
 *         ExecutionContext $context,
 *         RouteMatch $match
 *     ):Response {
 *
 *         return new Response(
 *             Standard::OK
 *         );
 *
 *     }
 *
 * }
 * </code>
 *
 * @since 1.0.0
 */
interface Controller {

    /**
     * ### Handles an HTTP request
     *
     * Executes the controller for the matched route.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched HTTP route and extracted parameters.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function __invoke (ExecutionContext $context, RouteMatch $match):Response;

}