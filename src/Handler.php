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

/**
 * ### Defines an HTTP request handler
 *
 * Represents a component capable of processing an HTTP request within an active HTTP execution context and returning
 * an HTTP response.
 *
 * Implementations do not manage application lifecycles, dependency injection scopes, or HTTP response emission.
 *
 * <code>
 * $response = $handler->handle($context);
 * </code>
 *
 * @since 1.0.0
 */
interface Handler {

    /**
     * ### Handles an HTTP request
     *
     * Processes the current HTTP request using its execution context.
     *
     * <code>
     * $response = $handler->handle($context);
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function handle (ExecutionContext $context):Response;

}