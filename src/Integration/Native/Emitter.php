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
    Emitter as HttpEmitter, Request, Response
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
 * Transmits HTTP responses through the native PHP Server API (SAPI).
 *
 * The emitter sends the response status and body using the native PHP execution environment.
 *
 * HTTP protocol rules determine whether response content may be transmitted.
 *
 * Materializable bodies are emitted directly, while readable bodies are transmitted sequentially in chunks.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Integration\Native\ {
 *     Factory, Emitter
 * };
 * use FireHub\Adapter\HTTP\Response;
 * use FireHub\Adapter\HTTP\Response\Status\Standard;
 * use FireHub\Adapter\HTTP\Body\Str;
 *
 * $request = Factory::fromGlobals()->create();
 *
 * $response = new Response(
 *     Standard::OK,
 *     new Str('Hello FireHub!')
 * );
 *
 * new Emitter()->emit($request, $response);
 *
 * // HTTP response status: 200 OK
 * // HTTP response body: Hello FireHub!
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Emitter implements HttpEmitter{

    /**
     * {@inheritDoc}
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
     * new Emitter()->emit($request, $response);
     *
     * // HTTP response status: 201 Created
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Emitter::emitHeaders() To emit the HTTP response headers.
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Emitter::emitBody() To emit the HTTP response body.
     * @uses \FireHub\Runtime\Http\Response::headersSent() To check if headers have already been sent.
     * @uses \FireHub\Runtime\Http\Response::statusCode() To set the HTTP status code.
     * @uses \FireHub\Adapter\HTTP\Response\Policy::allowsBody() To check if response content is permitted.
     *
     * @param \FireHub\Adapter\HTTP\Request $request <p>
     * HTTP request associated with the response.
     * </p>
     * @param \FireHub\Adapter\HTTP\Response $response <p>
     * HTTP response to emit.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\ResponseEmissionException If HTTP headers have already been sento rthe
     * body cannot be emitted.
     */
    public function emit (Request $request, Response $response):void {

        if (Runtime\Http\Response::headersSent($file, $line))
            throw new ResponseEmissionException(
                "Cannot emit HTTP response: headers already sent at $file:$line."
            );

        Runtime\Http\Response::statusCode(
            $response->status()->code()
        );

        $this->emitHeaders($response);

        if (Policy::allowsBody($request, $response))
            $this->emitBody($response);

    }

    /**
     * ### Emits HTTP response headers
     *
     * Sends all response header fields in their original order.
     *
     * Existing header fields with the same name are preserved, including multiple Set-Cookie fields.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response::headers() Gets response headers.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::all() Gets all header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() Gets the header name.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() Gets the header value.
     * @uses \FireHub\Runtime\Http\Response::sendHeader() Sends an HTTP header.
     *
     * @param \FireHub\Adapter\HTTP\Response $response <p>
     * HTTP response containing the headers to emit.
     * </p>
     *
     * @return void
     */
    private function emitHeaders (Response $response):void {

        foreach ($response->headers()->all() as $header)
            Runtime\Http\Response::sendHeader(
                $header['name']->value() . ': ' . $header['value']->value(),
                false
            );

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
     * @param \FireHub\Adapter\HTTP\Response $response <p>
     * HTTP response containing the body to emit.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\ResponseEmissionException If the body cannot be emitted.
     *
     * @return void
     */
    private function emitBody (Response $response):void {

        $body = $response->body();

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