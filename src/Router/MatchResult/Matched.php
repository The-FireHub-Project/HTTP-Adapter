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

use FireHub\Adapter\HTTP\Router\MatchResult;
use FireHub\Adapter\HTTP\Router\Route;

/**
 * ### Represents a successfully matched HTTP route
 *
 * Contains the matched route definition and parameter values extracted from the requested path.
 *
 * Parameter values are preserved as strings and are not automatically converted into integers or other
 * application-specific types.
 *
 * @since 1.0.0
 */
final readonly class Matched implements MatchResult {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Router\Route $route <p>
     * Successfully matched route.
     * </p>
     * @param array<non-empty-string, string> $parameters [optional] <p>
     * Named parameters extracted from the requested path.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Route $route,
        private array $parameters = []
    ) {}

    /**
     * ### Gets the matched route
     *
     * Returns the route definition associated with this result.
     *
     * <code>
     * $route = $result->route();
     *
     * $route->path()->value();
     *
     * // '/users/{id}'
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Router\Route Matched route.
     */
    public function route ():Route {

        return $this->route;

    }

    /**
     * ### Gets the extracted route parameters
     *
     * Returns all named parameter values captured during route matching.
     *
     * <code>
     * $result->parameters();
     *
     * // ['id' => '123']
     * </code>
     *
     * @since 1.0.0
     *
     * @return array<non-empty-string, string> Extracted route parameters.
     */
    public function parameters ():array {

        return $this->parameters;

    }

}