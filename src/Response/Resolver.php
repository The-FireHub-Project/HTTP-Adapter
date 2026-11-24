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

namespace FireHub\Adapter\HTTP\Response;

use FireHub\Adapter\HTTP\Router\ActionContext;
use FireHub\Adapter\HTTP\Response;
use FireHub\Adapter\HTTP\Response\Resolver\ {
    BodyResponse, EmptyResponse, Json, NativeResponse, Text, ValueResolver
};
use FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException;
use FireHub\Runtime;

/**
 * ### HTTP response resolver
 *
 * Coordinates the conversion of route action results into HTTP responses through an ordered collection
 * of value resolution strategies.
 *
 * Extracts explicitly supplied HTTP metadata from response result objects and delegates response
 * construction to the first registered resolver supporting the underlying value.
 *
 * Does not perform response serialization, rendering, header merging, or emission directly.
 * @since 1.0.0
 */
final readonly class Resolver {

    /**
     * ### Registered value resolvers
     *
     * Resolvers are evaluated in registration order.
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Response\Resolver\ValueResolver>
     */
    private array $resolvers;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To normalize resolver registration order.
     *
     * @param \FireHub\Adapter\HTTP\Response\Resolver\ValueResolver ...$resolvers <p>
     * Ordered value resolution strategies.
     * </p>
     *
     * @return void
     */
    public function __construct (ValueResolver ...$resolvers) {

        $this->resolvers = Runtime\Arr\Access::values($resolvers);

    }

    /**
     * ### Creates a response resolver with the default value resolution strategies
     *
     * Registers the standard HTTP response value resolvers in their default evaluation order.
     *
     * Existing HTTP responses and message bodies take precedence over scalar and serializable values.
     *
     * <code>
     * $resolver = Resolver::default();
     *
     * $response = $resolver->resolve(
     *     ['name' => 'FireHub'],
     *     $context
     * );
     *
     * $response->headers()->getContentType()?->value();
     * // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @return self HTTP response resolver containing the default value resolution strategies.
     */
    public static function default ():self {

        return new self(
            new NativeResponse,
            new BodyResponse,
            new EmptyResponse,
            new Text,
            new Json
        );

    }

    /**
     * ### Resolves a route action result into an HTTP response
     *
     * When the supplied value is a response result object, its underlying value and explicitly assigned
     * HTTP metadata are extracted before selecting a value resolver.
     *
     * Registered resolvers are evaluated in registration order. The first resolver supporting the
     * underlying value is responsible for constructing the HTTP response.
     *
     * Existing HTTP responses must be supplied directly rather than wrapped in response result objects.
     *
     * Nested response result objects are not supported.
     *
     * <code>
     * $response = $resolver->resolve(
     *     new Result(
     *         value: ['name' => 'FireHub'],
     *         status: Status\Standard::CREATED
     *     ),
     *     $context
     * );
     *
     * $response->status(); // Status\Standard::CREATED
     *
     * $response->headers()->getContentType()?->value();
     * // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Result::value() To retrieve the underlying response value.
     * @uses \FireHub\Adapter\HTTP\Response\Result::status() To retrieve explicitly assigned HTTP status.
     * @uses \FireHub\Adapter\HTTP\Response\Result::headers() To retrieve explicitly assigned HTTP headers.
     * @uses \FireHub\Adapter\HTTP\Response\Resolver\ValueResolver::supports() To select a value resolver.
     * @uses \FireHub\Adapter\HTTP\Response\Resolver\ValueResolver::resolve() To construct the HTTP response.
     *
     * @param mixed $value <p>
     * Route action result to resolve.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\ActionContext $context <p>
     * Current route action execution context.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException If the supplied
     * value is not supported or contains an unsupported response result structure.
     *
     * @return \FireHub\Adapter\HTTP\Response Resolved HTTP response.
     */
    public function resolve (mixed $value, ActionContext $context):Response {

        $status = null;
        $headers = null;

        if ($value instanceof Result) {

            $status = $value->status();
            $headers = $value->headers();
            $value = $value->value();

            if ($value instanceof Result)
                throw new UnsupportedResponseValueException(
                    'Nested response results are not supported.'
                );

            if ($value instanceof Response)
                throw new UnsupportedResponseValueException(
                    'An existing HTTP response must not be wrapped in a response result.'
                );

        }

        foreach ($this->resolvers as $resolver)
            if ($resolver->supports($value))
                return $resolver->resolve(
                    $value,
                    $context,
                    $status,
                    $headers
                );

        throw new UnsupportedResponseValueException(
            'No registered value resolver supports the supplied value.'
        );

    }

}