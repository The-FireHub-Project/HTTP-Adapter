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
 * ### Defines an HTTP response emitter
 *
 * Provides a transport-independent contract for transmitting HTTP responses to clients.
 *
 * Implementations are responsible for delivering response metadata and content through their underlying transport.
 *
 * HTTP protocol requirements must be respected regardless of the transport implementation.
 * @since 1.0.0
 */
interface Emitter {

    /**
     * ### Emits an HTTP response
     *
     * Transmits the HTTP response associated with the given request.
     *
     * The request provides the context required to apply HTTP protocol rules, including response body suppression
     * for HEAD requests.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request $request <p>
     * HTTP request associated with the response.
     * </p>
     * @param \FireHub\Adapter\HTTP\Response $response <p>
     * HTTP response to emit.
     * </p>
     *
     * @return void
     */
    public function emit (Request $request, Response $response):void;

}