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

namespace FireHub\Adapter\HTTP\Router;

/**
 * ### Defines a route parameter constraint
 *
 * Determines whether an extracted route parameter satisfies a specific matching requirement.
 *
 * Constraints operate on individual parameter values and do not modify the original route pattern.
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

}