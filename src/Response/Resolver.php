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
use FireHub\Adapter\HTTP\Response\Resolver\ValueResolver;
use FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException;
use FireHub\Runtime;

/**
 * ### HTTP response resolver
 *
 * Coordinates the conversion of route handler return values into HTTP responses through an ordered collection
 * of response value resolvers.
 *
 * Value resolvers are evaluated in registration order, and the first resolver supporting the supplied value
 * is responsible for its conversion.
 *
 * The resolver does not perform conversions directly, allowing additional response value types to be supported
 * without modifying the resolution mechanism.
 * @since 1.0.0
 */
final readonly class Resolver {

    /**
     * ### Registered response value resolvers
     *
     * Value resolvers stored in their evaluation order.
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Response\Resolver\ValueResolver>
     */
    private array $resolvers;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To preserve insertion order.
     *
     * @param \FireHub\Adapter\HTTP\Response\Resolver\ValueResolver ...$resolvers <p>
     * Response value resolvers in their evaluation order.
     * </p>
     *
     * @uses \array_values() To normalize the value resolver collection.
     */
    public function __construct (ValueResolver ...$resolvers) {

        $this->resolvers = Runtime\Arr\Access::values($resolvers);

    }

    /**
     * ### Resolves a value into an HTTP response
     *
     * Evaluates registered value resolvers in their configured order and delegates the conversion to the
     * first resolver supporting the supplied value.
     *
     * If no registered resolver supports the value, an exception is thrown.
     *
     * Exceptions thrown during value inspection or conversion are propagated to the caller.
     *
     * <code>
     * $response = $resolver->resolve(
     *     'Hello FireHub!',
     *     $context
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Resolver\ValueResolver::supports() To determine whether a value resolver
     * supports the supplied value.
     * @uses \FireHub\Adapter\HTTP\Response\Resolver\ValueResolver::resolve() To convert the supplied value into an
     * HTTP response.
     *
     * @param mixed $value <p>
     * Route handler return value to resolve.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\ActionContext $context <p>
     * Current route action context.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException If no registered
     * value resolver supports the supplied value.
     *
     * @return \FireHub\Adapter\HTTP\Response Resolved HTTP response.
     */
    public function resolve (mixed $value, ActionContext $context):Response {

        foreach ($this->resolvers as $resolver)
            if ($resolver->supports($value))
                return $resolver->resolve($value, $context);

        throw new UnsupportedResponseValueException(
            'No registered value resolver supports the supplied value.'
        );

    }

}