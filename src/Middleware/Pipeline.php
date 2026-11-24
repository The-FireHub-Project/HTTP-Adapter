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

namespace FireHub\Adapter\HTTP\Middleware;

use FireHub\Adapter\HTTP\ {
    ExecutionContext, Middleware, Response
};
use FireHub\Foundation\Pipeline as FoundationPipeline;
use Closure;

/**
 * ### Executes an HTTP middleware pipeline
 *
 * Composes HTTP middleware around a terminal request handler using the generic Foundation Pipeline.
 *
 * Middleware executes in registration order and may delegate processing, modify responses, or terminate execution.
 *
 * The middleware chain is assembled once during construction and can be reused across multiple HTTP requests.
 *
 * Middleware instances are resolved lazily through the dependency injection scope associated with each execution.
 * @since 1.0.0
 */
final readonly class Pipeline {

    /**
     * ### Underlying pipeline
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\Pipeline<\FireHub\Adapter\HTTP\ExecutionContext, \FireHub\Adapter\HTTP\Response>
     */
    private FoundationPipeline $pipeline;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param Closure(\FireHub\Adapter\HTTP\ExecutionContext):\FireHub\Adapter\HTTP\Response $handler <p>
     * Terminal HTTP request handler.
     * </p>
     * @param class-string<\FireHub\Adapter\HTTP\Middleware> ...$middleware <p>
     * HTTP middleware class references in execution order.
     * </p>
     *
     * @return void
     */
    public function __construct (Closure $handler, string ...$middleware) {

        $stages = [];

        foreach ($middleware as $component)
            $stages[] = new Stage($component);

        $this->pipeline = new FoundationPipeline(
            $handler,
            ...$stages
        );

    }

    /**
     * ### Executes the HTTP middleware pipeline
     *
     * Passes the HTTP execution context through the configured middleware chain.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Pipeline::handle() To execute the composed processing chain.
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function handle (ExecutionContext $context):Response {

        return $this->pipeline->handle($context);

    }

}