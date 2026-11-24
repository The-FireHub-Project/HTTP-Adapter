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

namespace FireHub\Adapter\HTTP\Router\Constraint;

use FireHub\Adapter\HTTP\Router\Constraint;
use FireHub\Runtime;

/**
 * ### Matches numeric route parameters
 *
 * Accepts non-empty strings containing ASCII decimal digits.
 *
 * Negative numbers, decimal separators, signs, and whitespace are not accepted.
 * @since 1.0.0
 */
final readonly class Number implements Constraint {

    /**
     * {@inheritDoc}
     *
     * <code>
     * $constraint = new Number();
     *
     * $constraint->matches('123');
     * // true
     *
     * $constraint->matches('-123');
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate decimal digits.
     */
    public function matches (string $value):bool {

        return Runtime\Str\SB\Regex::match(
            '/\A[0-9]+\z/',
            $value
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $constraint = new Number();
     *
     * $constraint->convert('00123');
     *
     * // '00123'
     * </code>
     *
     * @since 1.0.0
     *
     * @return string Original parameter value.
     */
    public function convert (string $value):string {

        return $value;

    }

}