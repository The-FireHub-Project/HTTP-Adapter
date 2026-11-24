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

use FireHub\Adapter\HTTP\Response\ {
    Headers, Status
};

/**
 * ### Represents an immutable HTTP response
 *
 * Encapsulates the HTTP response status and optional message body.
 *
 * Response instances are immutable and independent of the underlying HTTP server or transport implementation.
 *
 * The response body may contain materialized content or provide sequential access to streaming content.
 *
 * The response object is immutable, but reading a stream-backed body may advance the underlying stream position.
 * @since 1.0.0
 */
final readonly class Response {

    /**
     * ### Response headers
     * @since 1.0.0
     */
    private Headers $headers;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Headers::empty() To create an empty HTTP response headers collection.
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status <p>
     * HTTP response status.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Body $body [optional] <p>
     * HTTP response body.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * HTTP response headers.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Status $status,
        private ?Body $body = null,
        ?Headers $headers = null
    ) {

        $this->headers = $headers ?? Headers::empty();

    }

    /**
     * ### Create a response with a different HTTP status
     *
     * Returns a new response instance with the supplied HTTP status while preserving the existing message body
     * and headers.
     *
     * <code>
     * $response = new Response(Standard::OK);
     *
     * $created = $response->withStatus(Standard::CREATED);
     *
     * $response->status(); // Standard::OK
     * $created->status(); // Standard::CREATED
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status <p>
     * HTTP status to assign to the new response.
     * </p>
     *
     * @return self New response instance with the supplied HTTP status.
     */
    public function withStatus (Status $status):self {

        return new self(
            $status,
            $this->body,
            $this->headers
        );

    }

    /**
     * ### Create a response with a different message body
     *
     * Returns a new response instance with the supplied message body while preserving the existing HTTP status
     * and headers.
     *
     * <code>
     * $response = new Response(Standard::OK);
     *
     * $body = new \FireHub\Adapter\HTTP\Body\Str('Hello FireHub!');
     *
     * $modified = $response->withBody($body);
     *
     * $response->body(); // null
     * $modified->body() === $body; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Body $body <p>
     * HTTP message body to assign to the new response.
     * </p>
     *
     * @return self New response instance with the supplied message body.
     */
    public function withBody (Body $body):self {

        return new self(
            $this->status,
            $body,
            $this->headers
        );

    }

    /**
     * ### Create a response without a message body
     *
     * Returns a new response instance without a message body while preserving the existing HTTP status
     * and headers.
     *
     * <code>
     * $response = new Response(
     *     Standard::OK,
     *     new \FireHub\Adapter\HTTP\Body\Str('Hello FireHub!')
     * );
     *
     * $modified = $response->withoutBody();
     *
     * $modified->body(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return self New response instance without a message body.
     */
    public function withoutBody ():self {

        return new self(
            $this->status,
            null,
            $this->headers
        );

    }

    /**
     * ### Create a response with a different header collection
     *
     * Returns a new response instance with the supplied header collection while preserving the existing HTTP
     * status and message body.
     *
     * The supplied header collection replaces the existing collection entirely.
     *
     * <code>
     * $response = new Response(Standard::OK);
     *
     * $headers = $response->headers()->withContentType(
     *     \FireHub\Adapter\HTTP\Header\Content\Type::parse('application/json')
     * );
     *
     * $modified = $response->withHeaders($headers);
     *
     * $modified->headers() === $headers; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Response\Headers $headers <p>
     * HTTP header collection to assign to the new response.
     * </p>
     *
     * @return self New response instance with the supplied header collection.
     */
    public function withHeaders (Headers $headers):self {

        return new self(
            $this->status,
            $this->body,
            $headers
        );

    }

    /**
     * ### Gets the HTTP response status
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Response\Status HTTP response status.
     */
    public function status ():Status {

        return $this->status;

    }

    /**
     * ### Gets the HTTP response body
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Body HTTP response body.
     */
    public function body ():?Body {

        return $this->body;

    }

    /**
     * ### Gets the HTTP response headers
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Response\Headers HTTP response headers.
     */
    public function headers ():Headers {

        return $this->headers;

    }

}