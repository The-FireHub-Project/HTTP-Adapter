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
use FireHub\Core\Meta\Enum\Side;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Length Header
 *
 * Represents the Content-Length header field value.
 *
 * Content-Length specifies the decimal number of octets in the HTTP message content.
 *
 * The value is stored as a normalized decimal string to support lengths greater than PHP_INT_MAX without integer
 * overflow.
 *
 * Leading zeros are removed during construction, while zero itself is preserved.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Content\Length;
 *
 * $length = new Length(1024);
 *
 * $length->value(); // 1024
 * $length->bytes(); // 1024
 *
 * $length = Length::parse('00042');
 *
 * $length->value(); // 42
 *
 * $length = Length::parse('18446744073709551616');
 *
 * $length->bytes(); // null
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Length extends ValueObject {

    /**
     * ### Normalized decimal content length
     * @since 1.0.0
     *
     * @var non-empty-string
     */
    private string $length;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the content length.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate decimal syntax.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove leading zeros.
     *
     * @param non-negative-int|non-empty-string $length <p>
     * Non-negative content length expressed as an integer or an unsigned decimal string.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the content length is negative or
     * contains invalid characters.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (int|string $length) {

        $value = (string)$length;

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'+.^_`|~0-9A-Za-z-]+\z/",
                $value
            ),
            fn() => new InvalidFieldValueException(
                'Content-Length must be a non-negative decimal integer.'
            )
        );

        $normalized = Runtime\Str\SB\Transform::trim($value, Side::LEFT, '0');

        $this->length = $normalized === ''
            ? '0'
            : $normalized;

    }

    /**
     * ### Parses a Content-Length Header
     *
     * Parses a serialized Content-Length header field value.
     *
     * Only unsigned decimal digits are accepted.
     *
     * Whitespace, signs, decimal points, and comma-separated values are not accepted.
     *
     * <code>
     * $length = Length::parse('1024');
     *
     * $length->bytes(); // 1024
     *
     * $length = Length::parse('00042');
     *
     * $length->value(); // 42
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized Content-Length header field value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the content length is negative or
     * contains invalid characters.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Content-Length value object.
     */
    public static function parse (string $value):self {

        return new self($value);

    }

    /**
     * ### Gets the Content Length in Bytes
     *
     * Returns the content length as a PHP integer.
     *
     * Returns null when the content length exceeds PHP_INT_MAX and cannot be represented by the native integer type.
     *
     * <code>
     * $length = new Length(1024);
     *
     * $length->bytes(); // 1024
     *
     * $length = Length::parse('18446744073709551616');
     *
     * $length->bytes(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To compare the length with PHP_INT_MAX.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the string length.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To compare the length with PHP_INT_MAX.
     *
     * @return null|non-negative-int Content length in bytes, or null if it exceeds PHP_INT_MAX.
     */
    public function bytes ():?int {

        $maximum = (string)IntegerLimits::MAX;

        $length = Runtime\Str\SB\Inspection::length($this->length);
        $maximum_length = Runtime\Str\SB\Inspection::length($maximum);

        if ($length > $maximum_length) return null;

        if (
            $length === $maximum_length
            && Runtime\Str\SB\Compare::lexical($this->length, $maximum) > 0
        ) return null;

        /** @var non-negative-int */
        return (int)$this->length;

    }

    /**
     * ### Gets the Serialized Content Length
     *
     * Returns the normalized decimal representation of the Content-Length header field value.
     *
     * <code>
     * $length = new Length('00042');
     *
     * $length->value(); // 42
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Normalized decimal content length.
     */
    public function value ():string {

        return $this->length;

    }

}