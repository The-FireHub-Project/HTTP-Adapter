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
 * ### Cache-Control Min-Fresh Directive
 *
 * Represents the min-fresh request cache directive.
 *
 * Indicates that the client requires a response that will remain fresh for at least the specified number of seconds.
 *
 * The value must be a non-negative integer.
 *
 * <code>
 * $directive = new MinFresh(600);
 *
 * $directive->seconds(); // 600
 * $directive->value();   // min-fresh=600
 * </code>
 *
 * @since 1.0.0
 */
final readonly class MinFresh extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the freshness requirement.
     *
     * @param non-negative-int $seconds <p>
     * Minimum remaining freshness lifetime in seconds.
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
                'Cache-Control min-fresh cannot be negative.'
            )
        );

        parent::__construct(
            new Name('min-fresh'),
            new Value((string)$seconds)
        );

    }

    /**
     * ### Parses a Min-Fresh Directive
     *
     * Parses the min-fresh directive and validates its numeric value.
     *
     * Both token and quoted-string representations are accepted. Leading zeros are permitted and normalized.
     *
     * <code>
     * $directive = MinFresh::parse('min-fresh=600');
     *
     * $directive->seconds(); // 600
     * $directive->value();   // min-fresh=600
     *
     * $directive = MinFresh::parse('MIN-FRESH="000600"');
     *
     * $directive->seconds(); // 600
     * $directive->value();   // min-fresh=600
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To parse token or quoted-string syntax.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the directive name.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To validate the integer range.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate decimal digits.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove leading zeros.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To validate the integer length.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the integer value.
     *
     * @param non-empty-string $value <p>
     * Serialized min-fresh directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed min-fresh directive.
     */
    public static function parse (string $value):self {

        $parameter = Parameter\Quoted::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('min-fresh')))
            throw new InvalidParameterException(
                'Expected the Cache-Control min-fresh directive.'
            );

        $seconds = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $seconds))
            throw new InvalidParameterException(
                'Cache-Control min-fresh must contain decimal digits.'
            );

        $normalized = Runtime\Str\SB\Transform::trim(
            $seconds,
            Side::LEFT,
            '0'
        );

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
            'Cache-Control min-fresh exceeds the supported integer range.'
        );

        /** @var non-negative-int $normalized */
        $normalized = (int)$normalized;

        return new self($normalized);

    }

    /**
     * ### Gets the Minimum Freshness Requirement
     *
     * Returns the minimum remaining freshness lifetime in seconds.
     *
     * <code>
     * $directive = new MinFresh(600);
     *
     * $directive->seconds(); // 600
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-negative-int Minimum remaining freshness lifetime in seconds.
     */
    public function seconds ():int {

        return $this->seconds;

    }

}