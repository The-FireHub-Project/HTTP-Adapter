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

use FireHub\Core\Boundary\Pipeline\ {
    Next, Stage as StageBoundary
};
use FireHub\Adapter\HTTP\ {
    ExecutionContext, Middleware, Response
};
use FireHub\Adapter\HTTP\Exception\InvalidMiddlewareException;

/**
 * ### Adapts HTTP middleware to a generic pipeline stage
 *
 * Bridges the HTTP middleware contract with the generic pipeline execution contract.
 *
 * Middleware is resolved lazily through the current HTTP execution dependency injection scope.
 *
 * The stage retains only the middleware class reference and does not manage the lifecycle of resolved middleware
 * instances.
 * @since 1.0.0
 *
 * @implements \FireHub\Core\Boundary\Pipeline\Stage<\FireHub\Adapter\HTTP\ExecutionContext,\FireHub\Adapter\HTTP\Response>
 */
final readonly class Stage implements StageBoundary {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param class-string<\FireHub\Adapter\HTTP\Middleware> $middleware <p>
     * HTTP middleware class reference.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private string $middleware
    ) {}

    /**
     * ### Processes an HTTP pipeline stage
     *
     * Resolves the middleware through the current HTTP execution dependency injection scope and delegates request
     * processing to the resolved middleware instance.
     *
     * <code>
     * $stage = new Stage(AuthenticationMiddleware::class);
     *
     * $response = $stage->process($context, $next);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Middleware::process() To process the request.
     * @uses \FireHub\Adapter\HTTP\ExecutionContext::scope() To retrieve the current HTTP execution scope.
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $input <p>
     * Current HTTP execution context.
     * </p>
     * @param \FireHub\Core\Boundary\Pipeline\Next<ExecutionContext, \FireHub\Adapter\HTTP\Response> $next <p>
     * Continuation representing the remaining HTTP processing chain.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidMiddlewareException If the resolved middleware does not implement
     * the Middleware interface.
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function process (mixed $input, Next $next):Response {

        $middleware = $input->scope()->get($this->middleware);

        if (!$middleware instanceof Middleware)
            throw new InvalidMiddlewareException(
                "The resolved middleware '$this->middleware' must implement the Middleware interface."
            );

        return $middleware->process(
            $input,
            $next
        );

    }

}