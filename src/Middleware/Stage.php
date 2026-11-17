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

/**
 * ### Adapts HTTP middleware to a generic pipeline stage
 *
 * Bridges the HTTP middleware contract with the generic pipeline execution contract.
 *
 * Delegates processing to the wrapped middleware while preserving the HTTP execution context and response types.
 * @since 1.0.0
 *
 * @implements \FireHub\Core\Boundary\Pipeline\Stage<\FireHub\Adapter\HTTP\ExecutionContext,\FireHub\Adapter\HTTP\Response>
 */
final readonly class Stage implements StageBoundary {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Middleware $middleware <p>
     * HTTP middleware component.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Middleware $middleware
    ) {}

    /**
     * ### Processes an HTTP pipeline stage
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\ExecutionContext $input <p>
     * Current HTTP execution context.
     * </p>
     * @param \FireHub\Core\Boundary\Pipeline\Next<ExecutionContext, \FireHub\Adapter\HTTP\Response> $next <p>
     * Continuation representing the remaining HTTP processing chain.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response HTTP response.
     */
    public function process (mixed $input, Next $next):Response {

        return $this->middleware->process(
            $input,
            $next
        );

    }

}