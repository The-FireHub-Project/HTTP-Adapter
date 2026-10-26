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

namespace FireHub\Adapter\HTTP\Request\Method;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Runtime;
use FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException;

/**
 * ### Represents a custom HTTP request method
 *
 * Immutable value object representing an HTTP method token.
 *
 * Supports extension methods not defined by the Standard enumeration. Method tokens are case-sensitive and must
 * follow HTTP token syntax.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Custom extends ValueObject implements Method {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the HTTP method token.
     *
     * @param TValue $method <p>
     * HTTP method token.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If the method token is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $method
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match("/\\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\\z/", $method),
            fn() => new InvalidMethodNameException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->method;

    }

}