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

/**
 * ### Represents an automatically handled OPTIONS request
 *
 * Indicates that a requested path matches registered routes and supports automatic OPTIONS handling.
 *
 * Contains the HTTP methods allowed for the matched path.
 *
 * Does not generate an HTTP response.
 * @since 1.0.0
 */
final readonly class Options implements MatchResult {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param non-empty-list<\FireHub\Adapter\HTTP\Request\Method> $allowed_methods
     * Allowed HTTP methods.
     *
     * @return void
     */
    public function __construct (
        private array $allowed_methods
    ) {}

    /**
     * ### Gets the allowed HTTP methods
     *
     * <code>
     * $methods = $result->allowedMethods();
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