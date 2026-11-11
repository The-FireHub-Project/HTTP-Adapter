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

namespace FireHub\Adapter\HTTP;

use FireHub\Core\Boundary\Container\Scope;
use FireHub\Application\Application;
use FireHub\Application\ExecutionContext as ApplicationContext;

/**
 * ### Represents an HTTP request execution context
 *
 * Provides access to the HTTP request and the associated application execution context during a single HTTP
 * request execution.
 *
 * Each HTTP execution context is associated with exactly one request and one application execution scope.
 *
 * The application owns the scope lifecycle. The scope is automatically closed when execution completes.
 *
 * HTTP execution contexts do not manage application lifecycle transitions or dependency injection scopes.
 * @since 1.0.0
 */
final readonly class ExecutionContext {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request $request <p>
     * HTTP request associated with this execution.
     * </p>
     * @param \FireHub\Application\ExecutionContext $application_context <p>
     * Application execution context associated with this HTTP request.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Request $request,
        private ApplicationContext $application_context
    ) {}

    /**
     * ### Gets the HTTP request
     *
     * Returns the HTTP request associated with the current execution.
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request HTTP request.
     */
    public function request ():Request {

        return $this->request;

    }

    /**
     * ### Gets the application
     *
     * Returns the application associated with the current HTTP execution.
     * @since 1.0.0
     *
     * @uses \FireHub\Application\ExecutionContext::application() To get the associated application.
     *
     * @return \FireHub\Application\Application Associated application instance.
     */
    public function application ():Application {

        return $this->application_context->application();

    }

    /**
     * ### Gets the application execution context
     *
     * Returns the underlying application execution context associated with the current HTTP request.
     *
     * The application execution context provides access to the application and its isolated dependency injection
     * scope.
     * @since 1.0.0
     *
     * @return \FireHub\Application\ExecutionContext Application execution context.
     */
    public function applicationContext ():ApplicationContext {

        return $this->application_context;

    }

    /**
     * ### Gets the execution dependency injection scope
     *
     * Returns the isolated dependency injection scope associated with the current HTTP request.
     *
     * Scoped services are shared within the same HTTP execution but remain isolated from other executions.
     *
     * The scope is managed by the application and must not be manually closed by consumers.
     * @since 1.0.0
     *
     * @uses \FireHub\Application\ExecutionContext::scope() To get the execution scope.
     *
     * @return \FireHub\Core\Boundary\Container\Scope Execution scope.
     */
    public function scope ():Scope {

        return $this->application_context->scope();

    }

}