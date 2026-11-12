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

namespace FireHub\Adapter\HTTP\Integration\Native;

use FireHub\Adapter\HTTP\ {
    Adapter, Request
};
use FireHub\Adapter\HTTP\Integration\Native\Emitter;
use FireHub\Foundation\IO\Stream\Input;
use Closure;

/**
 * ### Represents a native HTTP exchange
 *
 * Associates an HTTP request with its native PHP input stream.
 *
 * The exchange owns the input stream and is responsible for releasing it after request processing completes.
 *
 * Request handling and response emission are delegated to the HTTP adapter.
 * @since 1.0.0
 */
final readonly class Exchange {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request $request <p>
     * HTTP request.
     * </p>
     * @param \FireHub\Foundation\IO\Stream\Input $input <p>
     * Native PHP input stream owned by this exchange.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Request $request,
        private Input $input
    ) {}

    /**
     * ### Gets the HTTP request
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Request HTTP request.
     */
    public function request ():Request {

        return $this->request;

    }

    /**
     * ### Checks whether the exchange is closed
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Input::isClosed() To check whether the native input stream is closed.
     *
     * @return bool True if the exchange is closed, false otherwise.
     */
    public function isClosed ():bool {

        return $this->input->isClosed();

    }

    /**
     * ### Closes the native HTTP exchange
     *
     * Releases the native PHP input stream owned by this exchange.
     *
     * Calling this method multiple times is safe.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Input::close() To close the native input stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If removing the filter fails.
     *
     * @return void
     */
    public function close ():void {

        $this->input->close();

    }

    /**
     * ### Serves the native HTTP exchange
     *
     * Executes the HTTP request handler and emits the response through the provided emitter.
     *
     * The HTTP adapter manages the application execution scope.
     *
     * The native input stream is automatically closed after execution, including when the handler or emitter throws
     * an exception.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Adapter::serve() To execute the HTTP request and emit its response.
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Exchange::close() To release the native input stream.
     *
     * @param \FireHub\Adapter\HTTP\Adapter $adapter <p>
     * HTTP adapter responsible for request execution.
     * </p>
     * @param Closure(\FireHub\Adapter\HTTP\ExecutionContext):\FireHub\Adapter\HTTP\Response $handler <p>
     * HTTP request handler.
     * </p>
     * @param \FireHub\Adapter\HTTP\Integration\Native\Emitter $emitter <p>
     * HTTP response emitter.
     * </p>
     *
     * @throws \FireHub\Kernel\Lifecycle\Exception\InvalidLifecycleStateException If the adapter is not initialized.
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidHttpAdapterStateException If the adapter is not available.
     * @throws \FireHub\Application\Exception\InvalidApplicationStateException If the application is not initialized.
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If removing the filter fails.
     *
     * @return void
     */
    public function serve (Adapter $adapter, Closure $handler, Emitter $emitter):void {

        try {

            $adapter->serve(
                $this->request,
                $handler,
                $emitter
            );

        } finally {

            $this->close();

        }

    }

}