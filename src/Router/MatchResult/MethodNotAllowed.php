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
 * ### Represents an HTTP method not allowed for a matched path
 *
 * Indicates that the requested path matches at least one registered route, but none accepts the requested HTTP method.
 *
 * Contains the HTTP methods allowed for the requested path.
 *
 * A dispatcher may translate this result into an HTTP 405 response containing the Allow response header.
 *
 * @since 1.0.0
 */
final readonly class MethodNotAllowed implements MatchResult {

    /**
     * ### Allowed HTTP methods
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Request\Method>
     */
    private array $allowedMethods;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the allowed methods list.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method ...$allowedMethods <p>
     * HTTP methods allowed for the requested path.
     * </p>
     *
     * @return void
     */
    public function __construct (
        Method ...$allowedMethods
    ) {

        $this->allowedMethods = Runtime\Arr\Access::values($allowedMethods);

    }

    /**
     * ### Gets the allowed HTTP methods
     *
     * Returns the HTTP methods supported by routes matching the requested path.
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
     * @return list<\FireHub\Adapter\HTTP\Request\Method> Allowed HTTP methods.
     */
    public function allowedMethods ():array {

        return $this->allowedMethods;

    }

}