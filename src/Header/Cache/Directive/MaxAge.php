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

namespace FireHub\Adapter\HTTP\Header\Cache\Directive;

use FireHub\Core\Meta\Enum\Side;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Adapter\HTTP\Header\{
    Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### Cache-Control Max-Age Directive
 *
 * Represents the max-age cache directive.
 *
 * Specifies the maximum age, in seconds, that a cached response may have before it is considered stale.
 *
 * The value must be a non-negative integer.
 *
 * <code>
 * $directive = new MaxAge(3600);
 *
 * $directive->seconds(); // 3600
 * $directive->value();   // max-age=3600
 * </code>
 *
 * @since 1.0.0
 */
final readonly class MaxAge extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the maximum age.
     *
     * @param non-negative-int $seconds <p>
     * Maximum age in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the value is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $seconds
    ) {

        $this->guard(
            fn():bool => $seconds >= 0,
            fn() => new InvalidParameterException(
                'Cache-Control max-age cannot be negative.'
            )
        );

        parent::__construct(
            new Name('max-age'),
            new Value((string)$seconds)
        );

    }

    /**
     * ### Parses a Max-Age Directive
     *
     * Parses the max-age directive and validates its numeric value.
     *
     * <code>
     * $directive = MaxAge::parse('max-age=3600');
     *
     * $directive->seconds(); // 3600
     * $directive->value();   // max-age=3600
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the name-value pair.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare the parameter name.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To validate the integer range.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the numeric value.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To validate the integer length.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the integer value.
     *
     * @param non-empty-string $value <p>
     * Serialized max-age directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed max-age directive.
     */
    public static function parse (string $value):self {

        $parameter = Parameter\Quoted::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('max-age')))
            throw new InvalidParameterException(
                'Expected the Cache-Control max-age directive.'
            );

        $seconds = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $seconds))
            throw new InvalidParameterException(
                'Cache-Control max-age must contain decimal digits.'
            );

        $normalized = Runtime\Str\SB\Transform::trim($seconds, Side::LEFT, '0');

        if ($normalized === '') return new self(0);

        $maximum = (string)IntegerLimits::MAX;
        $length = Runtime\Str\SB\Inspection::length($normalized);
        $maximum_length = Runtime\Str\SB\Inspection::length($maximum);

        if (
            $length > $maximum_length
            || (
                $length === $maximum_length
                && Runtime\Str\SB\Compare::lexical(
                    $normalized,
                    $maximum
                ) > 0
            )
        ) throw new InvalidParameterException(
            'Cache-Control max-age exceeds the supported integer range.'
        );

        /** @var non-negative-int $normalized */
        $normalized = (int)$normalized;

        return new self($normalized);

    }

    /**
     * ### Gets the Maximum Age
     *
     * Returns the maximum cache age in seconds.
     *
     * <code>
     * $directive = new MaxAge(3600);
     *
     * $directive->seconds(); // 3600
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-negative-int Maximum age in seconds.
     */
    public function seconds ():int {

        return $this->seconds;

    }

}