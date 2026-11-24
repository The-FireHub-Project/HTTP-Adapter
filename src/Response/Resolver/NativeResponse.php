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

use FireHub\Adapter\HTTP\Router\ActionContext;
use FireHub\Adapter\HTTP\ {
    Response\Headers, Response\Status, Response
};
use FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException;

/**
 * ### Native HTTP response value resolver
 *
 * Resolves existing HTTP response instances without modifying their status, headers, or body.
 *
 * The original response instance is returned unchanged, preserving its identity and contents.
 * @since 1.0.0
 */
final readonly class NativeResponse implements ValueResolver {

    /**
     * {@inheritDoc}
     *
     * <code>
     * $resolver = new NativeResponse();
     *
     * $resolver->supports(new Response(Standard::OK)); // true
     * $resolver->supports('Hello FireHub!'); // false
     * $resolver->supports(null); // false
     * </code>
     *
     * @since 1.0.0
     */
    public function supports (mixed $value):bool {

        return $value instanceof Response;

    }

    /**
     * {@inheritDoc}
     *
     * code>
     * $resolver = new NativeResponse();
     *
     * $response = new Response(Status\Standard::OK);
     *
     * $resolver->resolve($response, $context) === $response;
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException
     * If additional HTTP metadata is supplied for an existing response.
     */
    public function resolve (mixed $value, ActionContext $context, ?Status $status = null, ?Headers $headers = null):Response {

        if (!$value instanceof Response)
            throw new UnsupportedResponseValueException(
                'The supplied value is not an HTTP response.'
            );

        if ($status !== null || $headers !== null)
            throw new UnsupportedResponseValueException(
                'An existing HTTP response cannot receive additional resolution metadata.'
            );

        return $value;

    }

}