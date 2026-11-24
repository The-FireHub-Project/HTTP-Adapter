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
    Router\ActionContext, Response
};
use FireHub\Adapter\HTTP\Response\Status\Standard;
use FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException;

/**
 * ### Empty HTTP response value resolver
 *
 * Converts null route handler return values into HTTP responses without message bodies.
 *
 * Generated responses use HTTP status 204 No Content and do not include representation-specific headers.
 *
 * Empty strings, empty arrays, zero, and false are not considered empty response values and are handled by
 * their corresponding value resolvers.
 * @since 1.0.0
 */
final readonly class EmptyResponse implements ValueResolver {

    /**
     * {@inheritDoc}
     *
     * <code>
     * $resolver = new EmptyResponse();
     *
     * $resolver->supports(null); // true
     * $resolver->supports(''); // false
     * $resolver->supports([]); // false
     * $resolver->supports(0); // false
     * $resolver->supports(false); // false
     * </code>
     *
     * @since 1.0.0
     */
    public function supports (mixed $value):bool {

        return $value === null;

    }

    /**
     * {@inheritDoc}
     *
     * Converts a null value into an HTTP response without a message body.
     *
     * <code>
     * $resolver = new EmptyResponse();
     *
     * $response = $resolver->resolve(
     *     null,
     *     $context
     * );
     *
     * $response->status()->code(); // 204
     * $response->body(); // null
     * $response->headers()->getContentType(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::supports() To validate the supplied value.
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException If the supplied
     * value is not null.
     */
    public function resolve (mixed $value, ActionContext $context):Response {

        if (!$this->supports($value))
            throw new UnsupportedResponseValueException(
                'The supplied value cannot be resolved as an empty response.'
            );

        return new Response(Standard::NO_CONTENT);

    }

}