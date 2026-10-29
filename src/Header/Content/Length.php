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

namespace FireHub\Adapter\HTTP\Header\Content;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentLengthException;

/**
 * ### Represents an HTTP content length
 *
 * Defines the size of HTTP message content in octets (bytes).
 *
 * Content length must be a non-negative integer.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<numeric-string>
 */
final readonly class Length extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the content length.
     *
     * @param non-negative-int $length <p>
     * Content length in octets.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentLengthException If the content length is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $length
    ) {

        $this->guard(
            fn() => $length >= 0,
            fn() => new InvalidContentLengthException(
                'Content length cannot be negative.'
            )
        );

    }

    /**
     * ### Gets the content length
     * @since 1.0.0
     *
     * @return non-negative-int Content length in octets.
     */
    public function length ():int {

        return $this->length;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return numeric-string Serialized content length.
     */
    public function value ():string {

        return (string)$this->length;

    }

}