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

use FireHub\Adapter\HTTP\Response;
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
     * ### Resolves a value into an HTTP response
     *
     * Converts a supported route handler return value into an HTTP response.
     *
     * The current route action context provides access to request-specific information required during response
     * resolution.
     *
     * Implementations must reject unsupported values rather than silently converting them into an unrelated
     * representation.
     *
     * <code>
     * $response = $resolver->resolve(
     *     $value,
     *     $context
     * );
     *
     * $response->status();
     * $response->headers();
     * $response->body();
     * </code>
     *
     * @since 1.0.0
     *
     * @param mixed $value <p>
     * Supported route handler return value.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\ActionContext $context <p>
     * Current route action context.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Response Resolved HTTP response.
     */
    public function resolve (mixed $value, ActionContext $context):Response;

}