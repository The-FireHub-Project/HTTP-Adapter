<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.4
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
 * ### Cache-Control Max-Stale Directive
 *
 * Represents the max-stale request cache directive.
 *
 * Indicates that the client is willing to accept a stale response.
 *
 * When a value is provided, it specifies the maximum acceptable staleness in seconds.
 *
 * When no value is provided, the client accepts stale responses without specifying a maximum staleness.
 *
 * <code>
 * $directive = new MaxStale();
 *
 * $directive->seconds(); // null
 * $directive->value();   // max-stale
 *
 * $directive = new MaxStale(3600);
 *
 * $directive->seconds(); // 3600
 * $directive->value();   // max-stale=3600
 * </code>
 *
 * @since 1.0.0
 */
final readonly class MaxStale extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the staleness limit.
     *
     * @param null|non-negative-int $seconds <p>
     * Maximum acceptable staleness in seconds, or null for no specified limit.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the value is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private ?int $seconds = null
    ) {

        $this->guard(
            fn():bool => $seconds === null || $seconds >= 0,
            fn() => new InvalidParameterException(
                'Cache-Control max-stale cannot be negative.'
            )
        );

        parent::__construct(
            new Name('max-stale'),
            new Value($seconds === null ? '' : (string)$seconds)
        );

    }

    /**
     * ### Parses a Max-Stale Directive
     *
     * Parses the max-stale directive with or without a numeric argument.
     *
     * Both token and quoted-string numeric values are accepted. Leading zeros are permitted and normalized.
     *
     * <code>
     * $directive = MaxStale::parse('max-stale');
     *
     * $directive->seconds(); // null
     * $directive->value();   // max-stale
     *
     * $directive = MaxStale::parse('MAX-STALE="0003600"');
     *
     * $directive->seconds(); // 3600
     * $directive->value();   // max-stale=3600
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
     * Serialized max-stale directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed max-stale directive.
     */
    public static function parse (string $value):self {

        if (new Name($value)->equalsIgnoreCase(new Name('max-stale')))
            return new self;

        $parameter = Parameter\Quoted::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('max-stale')))
            throw new InvalidParameterException(
                'Expected the Cache-Control max-stale directive.'
            );

        $seconds = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $seconds))
            throw new InvalidParameterException(
                'Cache-Control max-stale must contain decimal digits.'
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
            'Cache-Control max-stale exceeds the supported integer range.'
        );

        /** @var non-negative-int $normalized */
        $normalized = (int)$normalized;

        return new self($normalized);

    }

    /**
     * ### Gets the Maximum Acceptable Staleness
     *
     * Returns the maximum acceptable staleness in seconds.
     *
     * Null indicates that no staleness limit was specified.
     *
     * <code>
     * $directive = new MaxStale(3600);
     *
     * $directive->seconds(); // 3600
     *
     * $directive = new MaxStale();
     *
     * $directive->seconds(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|non-negative-int Maximum acceptable staleness in seconds, or null.
     */
    public function seconds ():?int {

        return $this->seconds;

    }

    /**
     * ### Serializes the Max-Stale Directive
     *
     * Omits the equal sign when no staleness limit is specified.
     *
     * <code>
     * (new MaxStale())->value();     // max-stale
     * (new MaxStale(3600))->value(); // max-stale=3600
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized max-stale directive.
     */
    public function value ():string {

        return $this->seconds === null
            ? $this->name()->value()
            : parent::value();

    }

}