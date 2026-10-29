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
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException;

/**
 * ### Represents an HTTP header field name
 *
 * Immutable value object representing a validated HTTP field name.
 *
 * HTTP header names are case-insensitive. The original casing is preserved for serialization and presentation.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
readonly class Name extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the HTTP header field name.
     *
     * @param non-empty-string $name <p>
     * HTTP header field name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $name
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match("/\\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\\z/", $name),
            fn() => new InvalidFieldNameException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        /** @var TValue */
        return $this->name;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the string to lowercase.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->name);

    }

}