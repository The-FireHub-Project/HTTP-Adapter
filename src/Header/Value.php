<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;
use FireHub\Runtime;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;

/**
 * ### Represents an HTTP header field value
 *
 * Immutable value object containing a validated HTTP header value.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Value extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier value cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the HTTP header field value.
     *
     * @param TValue $value <p>
     * HTTP header field value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match("/[\x00-\x08\x0A-\x1F\x7F]/", $value),
            fn() => new InvalidFieldValueException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}