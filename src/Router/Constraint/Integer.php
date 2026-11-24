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
use FireHub\Adapter\HTTP\Router\Exception\InvalidRouteParameterException;
use FireHub\Runtime;

/**
 * ### Matches and converts integer route parameters
 *
 * Accepts canonical signed decimal integers within the PHP integer range.
 *
 * Positive and negative integers are supported.
 *
 * Leading zeros, positive signs, whitespace, decimal separators, and values outside the PHP integer range are
 * rejected.
 * @since 1.0.0
 */
final readonly class Integer implements Constraint {

    /**
     * {@inheritDoc}
     *
     * <code>
     * $constraint = new Integer();
     *
     * $constraint->matches('123');  // true
     * $constraint->matches('-123'); // true
     * $constraint->matches('001');  // false
     * $constraint->matches('12.5'); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate integer digits.
     * @uses \FireHub\Runtime\Filter::value() To validate integer values.
     */
    public function matches (string $value):bool {

        if (!Runtime\Str\SB\Regex::match(
            '/\A(?:0|-?[1-9][0-9]*)\z/',
            $value
        )) return false;

        return Runtime\Filter::value(
                $value,
                FILTER_VALIDATE_INT
            ) !== false;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $constraint = new Integer();
     *
     * $constraint->convert('123');
     *
     * // int(123)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Constraint\Integer::matches() To validate the value.
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\InvalidRouteParameterException If the value is not a valid
     * integer.
     *
     * @return int Converted integer.
     */
    public function convert (string $value):int {

        if (!$this->matches($value))
            throw new InvalidRouteParameterException(
                'The route parameter must be a valid integer.'
            );

        return (int)$value;

    }

}