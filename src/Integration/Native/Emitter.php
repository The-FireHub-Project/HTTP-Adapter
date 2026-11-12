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
    Request, Response
};
use FireHub\Adapter\HTTP\Response\Policy;
use FireHub\Adapter\HTTP\Body\ {
    Materializable, Readable
};
use FireHub\Adapter\HTTP\Exception\ResponseEmissionException;
use FireHub\Runtime;

/**
 * ### Emits HTTP responses using native PHP functions
 *
 * Sends HTTP response metadata through the native PHP SAPI.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Integration\Native\Emitter;
 * use FireHub\Adapter\HTTP\Response;
 * use FireHub\Adapter\HTTP\Response\Status\Standard;
 *
 * $response = new Response(
 *     Standard::OK
 * );
 *
 * $emitter = new Emitter($response);
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Emitter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request $request <p>
     * HTTP request to emit.
     * </p>
     * @param \FireHub\Adapter\HTTP\Response $response <p>
     * HTTP response to emit.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Request $request,
        private Response $response
    ) {}

    /**
     * ### Emits the HTTP response
     *
     * <code>
     * use FireHub\Adapter\HTTP\Integration\Native\Emitter;
     * use FireHub\Adapter\HTTP\Response;
     * use FireHub\Adapter\HTTP\Response\Status\Standard;
     *
     * $response = new Response(
     *     Standard::CREATED
     * );
     *
     * new Emitter($response)->emit();
     *
     * // HTTP response status: 201 Created
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Emitter::emitBody() To emit the HTTP response body.
     * @uses \FireHub\Runtime\Http\Response::headersSent() To check if headers have already been sent.
     * @uses \FireHub\Runtime\Http\Response::statusCode() To set the HTTP status code.
     * @uses \FireHub\Adapter\HTTP\Response\Policy::allowsBody() To check if response content is permitted.
     *
     * @throws \FireHub\Adapter\HTTP\Exception\ResponseEmissionException If HTTP headers have already been sento rthe
     * body cannot be emitted.
     *
     * @return void
     */
    public function emit ():void {

        if (Runtime\Http\Response::headersSent($file, $line))
            throw new ResponseEmissionException(
                "Cannot emit HTTP response: headers already sent at $file:$line."
            );

        Runtime\Http\Response::statusCode(
            $this->response->status()->code()
        );

        if (Policy::allowsBody($this->request, $this->response))
            $this->emitBody();

    }

    /**
     * ### Emits the HTTP response body
     *
     * Sends materialized content directly or reads streaming content sequentially until the end of the body.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response::body() To retrieve the HTTP response body.
     * @uses \FireHub\Adapter\HTTP\Body\Materializable::contents() To emit materialized content.
     * @uses \FireHub\Adapter\HTTP\Body\Readable::read() To emit streaming content.
     * @uses \FireHub\Adapter\HTTP\Body\Readable::eof() To check for the end of the streaming content.
     *
     * @throws \FireHub\Adapter\HTTP\Exception\ResponseEmissionException If the body cannot be emitted.
     *
     * @return void
     */
    private function emitBody ():void {

        $body = $this->response->body();

        if ($body === null) return;

        if ($body instanceof Materializable) {

            echo $body->contents();

            return;

        }

        if ($body instanceof Readable) {

            while (!$body->eof()) {

                $chunk = $body->read(8192);

                if ($chunk === '')
                    throw new ResponseEmissionException(
                        'Cannot emit HTTP response body: no data was returned before EOF.'
                    );

                echo $chunk;

            }

            return;

        }

        throw new ResponseEmissionException(
            'Cannot emit HTTP response body: unsupported body implementation.'
        );

    }

}