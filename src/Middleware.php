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

use FireHub\Core\Boundary\Pipeline\Next;

/**
 * ### Defines an HTTP middleware component
 *
 * Represents a processing component that participates in HTTP request execution.
 *
 * Middleware receives the current HTTP execution context and a continuation representing the remaining processing
 * chain.
 *
 * Middleware may inspect the request, access application-scoped dependencies, delegate processing to the next
 * middleware, modify the returned response, or terminate execution by returning a response directly.
 *
 * Middleware implementations should not retain request-specific state between executions.
 * @since 1.0.0
 */
interface Middleware {

    /**
     * ### Processes an HTTP request
     *
     * Executes middleware logic within the current HTTP request scope.
     *
     * Calling the continuation delegates processing to the remaining middleware and eventually to the terminal
     * request handler.
     *
     * Returning a response without calling the continuation terminates the remaining processing chain.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     * @param \FireHub\Core\Boundary\Pipeline\Next<\FireHub\Adapter\HTTP\ExecutionContext,\FireHub\Adapter\HTTP\Response> $next <p>
     * Continuation representing the remaining HTTP processing chain.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function process (ExecutionContext $context, Next $next):Response;

}