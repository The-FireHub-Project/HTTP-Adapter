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

namespace FireHub\Adapter\HTTP\Router\Attribute;

use FireHub\Adapter\HTTP\Request\Method\Standard;
use Attribute;

/**
 * ### Declares an HTTP PUT route on a controller action
 *
 * Associates a controller action with the HTTP PUT request method, a route path pattern, and an optional route name.
 *
 * Multiple PUT route attributes may be declared on the same controller action, allowing the action to handle
 * different route path patterns.
 *
 * This attribute provides declarative routing metadata without performing route registration, route matching,
 * or controller execution.
 *
 * <code>
 * #[Put('/users/{id}', 'users.replace')]
 * public function replace (int $id):Response {
 *
 *     // Controller action implementation.
 *
 * }
 * </code>
 *
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Put extends Route {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Route::__construct() To initialize the route metadata.
     *
     * @param non-empty-string $pattern <p>
     * HTTP route path pattern.
     * </p>
     * @param null|non-empty-string $name [optional] <p>
     * Unique route name, or null if the route is unnamed.
     * </p>
     *
     * @return void
     */
    public function __construct (string $pattern, ?string $name = null) {

        parent::__construct(
            Standard::PUT,
            $pattern,
            $name
        );

    }

}