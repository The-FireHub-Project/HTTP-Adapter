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

namespace FireHub\Adapter\HTTP\Header\Cookie;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie Name
 *
 * Represents an immutable HTTP cookie name.
 *
 * Cookie names must conform to the HTTP token syntax and cannot contain whitespace, separators, control characters,
 * or non-ASCII characters.
 *
 * Cookie names are case-sensitive. Names that differ only in letter casing represent different cookies.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Cookie\Name;
 *
 * $name = new Name('session_id');
 *
 * $name->value();
 * // session_id
 *
 * $first = new Name('Session');
 * $second = new Name('session');
 *
 * $first->equals($second);
 * // false
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Name extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the cookie name.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP token syntax.
     *
     * @param non-empty-string $name <p>
     * Cookie name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie name contains invalid
     * characters.
     * @throws \FireHub\Core\Exception\FireHubException If the validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $name
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
                $name
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP cookie name.'
            )
        );

    }

    /**
     * ### Parses a Cookie Name
     *
     * Parses a cookie name from its serialized representation.
     *
     * The name is validated without trimming or case normalization.
     *
     * <code>
     * $name = Name::parse('session_id');
     *
     * $name->value();
     * // session_id
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized cookie name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie name contains invalid
     * characters.
     * @throws \FireHub\Core\Exception\FireHubException If the validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed cookie name.
     */
    public static function parse (string $value):self {

        return new self($value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $name = new Name('session_id');
     *
     * $name->value();
     * // session_id
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->name;

    }

}