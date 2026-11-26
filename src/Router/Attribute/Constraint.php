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

use FireHub\Adapter\HTTP\Router\Constraint as RouteConstraint;
use Attribute;

/**
 * ### Declares a route parameter constraint on a controller action
 *
 * Associates a named route parameter with a constraint that validates and converts its extracted value
 * during route matching.
 *
 * Multiple constraint attributes may be declared on the same controller action, allowing individual
 * constraints to be assigned to different route parameters.
 *
 * This attribute provides declarative routing metadata without performing parameter validation,
 * conversion, or route registration.
 *
 * <code>
 * #[Get('/users/{id}')]
 * #[Constraint('id', new Integer)]
 * public function show (int $id):Response {
 *
 *     // Controller action implementation.
 *
 * }
 * </code>
 *
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Constraint {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param non-empty-string $parameter <p>
     * Route parameter name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Constraint $constraint <p>
     * Constraint associated with the route parameter.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private string $parameter,
        private RouteConstraint $constraint
    ) {}

    /**
     * ### Gets the route parameter name
     *
     * <code>
     * $constraint = new Constraint('id', new Integer);
     *
     * $constraint->parameter(); // 'id'
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Route parameter name.
     */
    public function parameter ():string {

        return $this->parameter;

    }

    /**
     * ### Gets the route parameter constraint
     *
     * <code>
     * $constraint = new Constraint('id', new Integer);
     *
     * $constraint->constraint()->matches('123'); // true
     * $constraint->constraint()->convert('123'); // int(123)
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Router\Constraint Route parameter constraint.
     */
    public function constraint ():RouteConstraint {

        return $this->constraint;

    }

}