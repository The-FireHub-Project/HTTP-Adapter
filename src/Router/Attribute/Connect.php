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
 * ### Declares HTTP CONNECT routing metadata on a controller action
 *
 * Associates a controller action with the HTTP CONNECT request method, a route pattern, and an optional route name.
 *
 * Multiple CONNECT route attributes may be declared on the same controller action.
 *
 * This attribute provides declarative routing metadata without performing route registration, route matching,
 * controller execution, or tunnel establishment.
 *
 * Actual CONNECT request dispatch requires routing and transport support for authority-form request targets.
 *
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Connect extends Route {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Attribute\Route::__construct() To initialize the route metadata.
     *
     * @param non-empty-string $pattern <p>
     * Route pattern whose interpretation depends on the routing implementation.
     * </p>
     * @param null|non-empty-string $name [optional] <p>
     * Unique route name, or null if the route is unnamed.
     * </p>
     *
     * @return void
     */
    public function __construct (string $pattern, ?string $name = null) {

        parent::__construct(
            Standard::CONNECT,
            $pattern,
            $name
        );

    }

}