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

use FireHub\Core\Meta\Enum\Side;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Adapter\HTTP\Header\Value;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Length Header
 *
 * Represents the length of an HTTP message body in bytes.
 *
 * The length must be a non-negative integer.
 *
 * <code>
 * $length = new Length(1024);
 *
 * $length->bytes(); // 1024
 * $length->value(); // "1024"
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Length extends Value {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the byte count.
     *
     * @param non-negative-int $bytes <p>
     * Number of bytes in the message body.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the byte count is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $bytes
    ) {

        $this->guard(
            fn():bool => $bytes >= 0,
            fn() => new InvalidFieldValueException(
                'Content-Length cannot be negative.'
            )
        );

        parent::__construct((string)$bytes);

    }

    /**
     * ### Parses a Content-Length Header Value
     *
     * Accepts only decimal digits without signs or whitespace.
     *
     * <code>
     * $length = Length::parse('1024');
     *
     * $length->bytes(); // 1024
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To validate the integer range.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the decimal representation.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim leading zeros.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the string length.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the integer representation.
     *
     * @param string $value <p>
     * Serialized Content-Length header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the value is invalid or exceeds PHP_INT_MAX.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Content-Length value.
     */
    public static function parse (string $value):self {

        if (!Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $value))
            throw new InvalidFieldValueException(
                'Content-Length must contain only decimal digits.'
            );

        $normalized = Runtime\Str\SB\Transform::trim($value, Side::LEFT, '0');

        if ($normalized === '') return new self(0);

        $maximum = (string)IntegerLimits::MAX;

        $length = Runtime\Str\SB\Inspection::length($normalized);
        $maximum_length = Runtime\Str\SB\Inspection::length($maximum);

        if (
            $length > $maximum_length
            || (
                $length === $maximum_length
                && Runtime\Str\SB\Compare::lexical($normalized, $maximum) > 0
            )
        ) throw new InvalidFieldValueException(
            'Content-Length exceeds the supported integer range.'
        );

        /** @var non-negative-int $normalized */
        $normalized = (int)$normalized;

        return new self($normalized);

    }

    /**
     * ### Returns the Content Length in Bytes
     *
     * <code>
     * $length = new Length(1024);
     *
     * $length->bytes(); // 1024
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-negative-int Number of bytes.
     */
    public function bytes ():int {

        return $this->bytes;

    }

}