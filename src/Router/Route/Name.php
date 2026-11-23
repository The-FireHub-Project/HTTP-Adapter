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

namespace FireHub\Adapter\HTTP\Router\Route;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Router\Exception\InvalidRouteNameException;
use FireHub\Runtime;

/**
 * ### Represents a validated HTTP route name
 *
 * Encapsulates an immutable route identifier used to reference registered HTTP routes independently of their URI
 * patterns.
 *
 * Route names are case-sensitive and preserve their original values.
 *
 * Empty names and names containing ASCII control characters are not permitted.
 *
 * Route names do not enforce a particular naming convention.
 *
 * <code>
 * $name = new Name('users.show');
 *
 * $name->value();
 *
 * // 'users.show'
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Name extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To enforce the invariant that a route name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the route name.
     *
     * @param non-empty-string $value <p>
     * Route name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteNameException If the route name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        $this->guard(
            fn():bool => $value !== '',
            fn():InvalidRouteNameException => new InvalidRouteNameException(
                'Route name cannot be empty.'
            )
        );

        $this->guard(
            fn():bool => !Runtime\Str\SB\Regex::match('/[\x00-\x1F\x7F]/', $value),
            fn():InvalidRouteNameException => new InvalidRouteNameException(
                'Route name cannot contain ASCII control characters.'
            )
        );


    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $name = new Name('users.show');
     *
     * $name->value();
     *
     * // 'users.show'
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}