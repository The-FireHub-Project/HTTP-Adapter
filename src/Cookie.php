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

namespace FireHub\Adapter\HTTP;

use FireHub\Core\Type\ValueObject;
use FireHub\Runtime;
use FireHub\Adapter\HTTP\Cookie\Exception\ {
    InvalidCookieNameException, InvalidCookieValueException
};

/**
 * ### Represents the foundation of an HTTP cookie
 *
 * Provides common cookie name and value storage, validation, and access for request and response cookies.
 *
 * Cookie instances are immutable.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
abstract readonly class Cookie extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the cookie name and value.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the cookie name and value.
     *
     * @param non-empty-string $name <p>
     * HTTP cookie name.
     * </p>
     * @param string $value <p>
     * HTTP cookie value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieNameException If the cookie name is invalid.
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieValueException If the cookie value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        protected string $name,
        protected string $value
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\\z/",
                $name
            ),
            fn() => new InvalidCookieNameException
        );

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A[\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]*\z/',
                $value
            ),
            fn() => new InvalidCookieValueException
        );

    }

    /**
     * ### Gets the HTTP cookie name
     * @since 1.0.0
     *
     * @return non-empty-string Cookie name.
     */
    public function name ():string {

        return $this->name;

    }

    /**
     * ### Gets the HTTP cookie value
     * @since 1.0.0
     *
     * @return string Cookie value.
     */
    public function value ():string {

        return $this->value;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<mixed> Comparison value.
     */
    protected function comparisonValue():array {

        return [
            $this->name,
            $this->value
        ];

    }

}