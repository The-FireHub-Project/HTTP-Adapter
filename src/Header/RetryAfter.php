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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidRetryAfterException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Retry-After header
 *
 * Indicates how long a client should wait before making a follow-up request.
 *
 * Supports either a non-negative number of seconds or an HTTP date.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class RetryAfter extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the delay.
     * @uses \FireHub\Runtime\DataIs::int() To check whether the value is an integer.
     *
     * @param non-negative-int|\FireHub\Adapter\HTTP\Header\Date $value <p>
     * Retry delay in seconds or an absolute HTTP date.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidRetryAfterException If the delay is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int|Date $value
    ) {

        if (Runtime\DataIs::int($value)) {

            $this->guard(
                fn() => $value >= 0,
                fn() => new InvalidRetryAfterException(
                    'Retry-After delay cannot be negative.'
                )
            );

        }

    }

    /**
     * ### Checks whether the value represents a delay
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::int() To check whether the value is an integer.
     *
     * @return bool True if the value is expressed in seconds.
     */
    public function isDelay ():bool {

        return Runtime\DataIs::int($this->value);

    }

    /**
     * ### Checks whether the value represents an HTTP date
     * @since 1.0.0
     *
     * @return bool True if the value is an HTTP date.
     */
    public function isDate ():bool {

        return $this->value instanceof Date;

    }

    /**
     * ### Gets the retry delay in seconds
     *
     * Returns null when the value represents an HTTP date.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::int() To check whether the value is an integer.
     *
     * @return null|non-negative-int Retry delay in seconds.
     */
    public function delay ():?int {

        return Runtime\DataIs::int($this->value)
            ? $this->value
            : null;

    }

    /**
     * ### Gets the retry date
     *
     * Returns null when the value represents a delay in seconds.
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Date HTTP retry date.
     */
    public function date ():?Date {

        return $this->value instanceof Date
            ? $this->value
            : null;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Date::value() To serialize the HTTP date.
     */
    public function value ():string {

        if ($this->value instanceof Date)
            return $this->value->value();

        return (string)$this->value;

    }

}