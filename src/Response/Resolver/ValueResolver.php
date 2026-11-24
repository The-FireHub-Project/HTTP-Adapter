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

namespace FireHub\Adapter\HTTP\Response\Resolver;

use FireHub\Adapter\HTTP\ {
    Response\Headers, Response\Status, Response
};
use FireHub\Adapter\HTTP\Router\ActionContext;

/**
 * ### Defines an HTTP response value resolver
 *
 * Represents a strategy responsible for converting supported route handler return values into HTTP responses.
 *
 * Implementations determine whether a value can be resolved and perform the corresponding conversion using the
 * current route action context when necessary.
 *
 * Checking whether a value is supported must not modify the value, consume its contents, or produce side effects.
 *
 * Value resolvers do not execute route handlers, manage application lifecycles, or emit HTTP responses.
 *
 * <code>
 * if ($resolver->supports($value))
 *     $response = $resolver->resolve(
 *         $value,
 *         $context
 *     );
 * </code>
 *
 * @since 1.0.0
 */
interface ValueResolver {

    /**
     * ### Checks whether a value can be resolved
     *
     * Determines whether this resolver supports the supplied value without performing the conversion.
     *
     * This operation must not modify the supplied value, consume its contents, or produce side effects.
     *
     * <code>
     * $resolver->supports('Hello FireHub!');
     *
     * $resolver->supports([
     *     'message' => 'Hello FireHub!'
     * ]);
     * </code>
     *
     * @since 1.0.0
     *
     * @param mixed $value <p>
     * Route handler return value to inspect.
     * </p>
     *
     * @return bool True if the value can be resolved, false otherwise.
     */
    public function supports (mixed $value):bool;

    /**
     * ### Resolves a supported value into an HTTP response
     *
     * Uses explicitly supplied HTTP metadata when available and provides appropriate defaults otherwise.
     *
     * <code>
     * $response = $resolver->resolve(
     *     'FireHub',
     *     $context,
     *     Status\Standard::OK,
     *     Headers::empty()
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @param mixed $value <p>
     * Value to resolve into an HTTP response.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\ActionContext $context <p>
     * Current route action execution context.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Status $status [optional] <p>
     * Explicit HTTP status, or null to use the resolver default.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * Explicit HTTP headers, or null if no headers are supplied.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException
     * If the supplied value is not supported.
     *
     * @return \FireHub\Adapter\HTTP\Response Resolved HTTP response.
     */
    public function resolve (mixed $value, ActionContext $context, ?Status $status = null, ?Headers $headers = null):Response;

}