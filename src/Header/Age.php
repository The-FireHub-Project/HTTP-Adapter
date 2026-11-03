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
use FireHub\Adapter\HTTP\Header\Exception\InvalidAgeException;

/**
 * ### Represents an HTTP Age header
 *
 * Indicates the estimated age of a response in seconds.
 *
 * The age represents the time elapsed since the response was generated or successfully validated by the origin
 * server, as estimated according to HTTP caching rules.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<numeric-string>
 */
final readonly class Age extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the age.
     *
     * @param non-negative-int $seconds <p>
     * Estimated response age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidAgeException If the age is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $seconds
    ) {

        $this->guard(
            fn() => $seconds >= 0,
            fn() => new InvalidAgeException(
                'Age cannot be negative.'
            )
        );

    }

    /**
     * ### Gets the estimated response age
     * @since 1.0.0
     *
     * @return non-negative-int Estimated age in seconds.
     */
    public function seconds ():int {

        return $this->seconds;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return numeric-string Serialized Age header value.
     */
    public function value ():string {

        return (string)$this->seconds;

    }

}