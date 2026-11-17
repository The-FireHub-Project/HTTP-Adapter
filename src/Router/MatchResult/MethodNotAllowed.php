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

namespace FireHub\Adapter\HTTP\Router\MatchResult;

use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Adapter\HTTP\Router\MatchResult;
use FireHub\Runtime;

/**
 * ### Represents an HTTP method mismatch
 *
 * Indicates that the requested path matches one or more registered routes, but none supports the requested HTTP method.
 *
 * Contains a non-empty collection of unique allowed HTTP methods.
 *
 * The result does not generate HTTP response headers.
 * @since 1.0.0
 */
final readonly class MethodNotAllowed implements MatchResult {

    /**
     * ### Allowed HTTP methods
     * @since 1.0.0
     *
     * @var non-empty-list<\FireHub\Adapter\HTTP\Request\Method>
     */
    private array $allowed_methods;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To identify HTTP methods.
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex allowed methods.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $first <p>
     * First allowed HTTP method.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Method ...$others <p>
     * Additional allowed HTTP methods.
     * </p>
     *
     * @return void
     */
    public function __construct (Method $first, Method ...$others) {

        $methods = [];

        foreach ([$first, ...$others] as $method)
            $methods[$method->value()] = $method;

        /** @var non-empty-list<\FireHub\Adapter\HTTP\Request\Method> $methods */
        $methods = Runtime\Arr\Access::values($methods);

        $this->allowed_methods = $methods;

    }

    /**
     * ### Gets the allowed HTTP methods
     *
     * Returns a non-empty list of unique HTTP methods supported by the matching route path.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\MatchResult\MethodNotAllowed;
     * use FireHub\Adapter\HTTP\Request\Method\Standard;
     *
     * $result = new MethodNotAllowed(
     *     Standard::GET,
     *     Standard::POST
     * );
     *
     * $result->allowedMethods();
     *
     * // [Standard::GET, Standard::POST]
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-list<\FireHub\Adapter\HTTP\Request\Method> Allowed HTTP methods.
     */
    public function allowedMethods ():array {

        return $this->allowed_methods;

    }

}