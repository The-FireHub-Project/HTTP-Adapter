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

namespace FireHub\Adapter\HTTP\Response\Resolver;

use FireHub\Adapter\HTTP\ {
    Router\ActionContext, Response\Headers, Response\Status, Body, Response
};
use FireHub\Adapter\HTTP\Response\Status\Standard;
use FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException;

/**
 * ### HTTP body response value resolver
 *
 * Converts existing HTTP message bodies into HTTP responses without modifying or consuming their contents.
 *
 * Generated responses use HTTP status 200 OK and preserve the original body instance.
 *
 * Representation-specific headers are not inferred because the body does not necessarily expose information
 * about its media type, encoding, or intended disposition.
 *
 * Both materialized and stream-backed HTTP bodies are supported.
 * @since 1.0.0
 */
final readonly class BodyResponse implements ValueResolver {

    /**
     * {@inheritDoc}
     *
     * <code>
     * $resolver = new BodyResponse();
     *
     * $resolver->supports(new \FireHub\Adapter\HTTP\Body\Str('FireHub')); // true
     * $resolver->supports('FireHub'); // false
     * $resolver->supports(null); // false
     * </code>
     *
     * @since 1.0.0
     */
    public function supports (mixed $value):bool {

        return $value instanceof Body;

    }

    /**
     * {@inheritDoc}
     *
     * Wraps an existing HTTP message body in an HTTP response while preserving its identity and contents.
     *
     * The body is not read, copied, materialized, or otherwise modified during response resolution.
     *
     * <code>
     * $resolver = new BodyResponse();
     *
     * $body = new \FireHub\Adapter\HTTP\Body\Str('Hello FireHub!');
     *
     * $response = $resolver->resolve(
     *     $body,
     *     $context
     * );
     *
     * $response->status()->code(); // 200
     * $response->body() === $body; // true
     * $response->headers()->getContentType(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response::__construct() To wrap the supplied HTTP body in an HTTP response.
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException If the supplied
     * value is not an HTTP message body.
     */
    public function resolve (mixed $value, ActionContext $context, ?Status $status = null, ?Headers $headers = null):Response {

        if (!$value instanceof Body)
            throw new UnsupportedResponseValueException(
                'The supplied value is not an HTTP message body.'
            );

        return new Response(
            $status ?? Standard::OK,
            $value,
            $headers
        );

    }

}