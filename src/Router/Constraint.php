<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Router;

/**
 * ### Defines a route parameter constraint
 *
 * Validates an extracted route parameter and converts it into the PHP type defined by the route.
 *
 * Constraints operate on individual parameter values.
 *
 * The matches() method determines whether the parameter is accepted.
 * The convert() method transforms an already validated parameter.
 * @since 1.0.0
 */
interface Constraint {

    /**
     * ### Checks whether a parameter value satisfies the constraint
     *
     * <code>
     * $constraint = new Number();
     *
     * $constraint->matches('123');
     * // true
     *
     * $constraint->matches('abc');
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @param string $value <p>
     * Parameter value.
     * </p>
     *
     * @return bool True if the value satisfies the constraint.
     */
    public function matches (string $value):bool;

    /**
     * ### Converts a validated route parameter
     *
     * The value must satisfy matches() before conversion.
     *
     * <code>
     * $constraint = new Integer();
     *
     * $value = $constraint->convert('123');
     *
     * // int(123)
     * </code>
     *
     * @since 1.0.0
     *
     * @param string $value <p>
     * alidated route parameter.
     * </p>
     *
     * @return scalar Converted parameter value.
     */
    public function convert (string $value):int|float|string|bool;

}