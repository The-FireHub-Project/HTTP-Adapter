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
 * ### HTTP Cookie Value
 *
 * Represents an immutable HTTP cookie value.
 *
 * Cookie values must conform to the cookie-octet syntax defined by RFC 6265.
 *
 * Empty values are permitted. Non-empty values may contain only the ASCII characters allowed by the cookie-octet
 * grammar.
 *
 * Surrounding double quotation marks are accepted when parsing serialized cookie values but are not retained.
 *
 * This class does not perform URL encoding, Base64 encoding, or application-specific decoding.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Cookie\Value;
 *
 * $value = new Value('abc123');
 *
 * $value->value();
 * // abc123
 *
 * $value = Value::parse('"abc123"');
 *
 * $value->value();
 * // abc123
 *
 * $value = new Value('');
 *
 * $value->value();
 * // ''
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Value extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the cookie value.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate cookie-octet syntax.
     *
     * @param string $value <p>
     * Cookie value without surrounding quotation marks.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie value contains
     * characters prohibited by RFC 6265.
     * @throws \FireHub\Core\Exception\FireHubException If the validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A[\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]*\z/',
                $value
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP cookie value.'
            )
        );

    }

    /**
     * ### Parses a Cookie Value
     *
     * Parses a serialized HTTP cookie value.
     *
     * Optionally, surrounding double quotation marks are removed before validating the cookie-octet characters.
     *
     * Quotation marks must enclose the entire value. Embedded quotation marks and backslash escaping are not supported.
     *
     * <code>
     * $value = Value::parse('"abc123"');
     *
     * $value->value();
     * // abc123
     *
     * $value = Value::parse('abc123');
     *
     * $value->value();
     * // abc123
     *
     * $value = Value::parse('""');
     *
     * $value->value();
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the cookie value.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the string length.
     *
     * @param string $value <p>
     * Serialized HTTP cookie value, optionally enclosed in double quotation marks.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized cookie value is
     * malformed.
     * @throws \FireHub\Core\Exception\FireHubException If the validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed cookie value.
     */
    public static function parse (string $value):self {

        if ($value !== '' && $value[0] === '"') {

            $length = Runtime\Str\SB\Inspection::length($value);

            if ($length < 2 || $value[$length - 1] !== '"')
                throw new InvalidFieldValueException(
                    'Invalid quoted HTTP cookie value.'
                );

            return new self(Runtime\Str\SB\Access::part($value, 1, -1));

        }

        return new self($value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $value = Value::parse('"abc123"');
     *
     * $value->value();
     * // abc123
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}