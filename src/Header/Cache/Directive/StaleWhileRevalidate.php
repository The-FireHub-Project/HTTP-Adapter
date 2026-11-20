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
 * ### Cache-Control Stale-While-Revalidate Directive
 *
 * Represents the stale-while-revalidate response cache directive.
 *
 * Allows a cache to serve a stale response while asynchronously revalidating it with the origin server.
 *
 * The specified number of seconds defines the additional period after the response becomes stale during which this
 * behavior is permitted.
 *
 * The value must be a non-negative integer.
 *
 * <code>
 * $directive = new StaleWhileRevalidate(600);
 *
 * $directive->seconds(); // 600
 * $directive->value();   // stale-while-revalidate=600
 * </code>
 *
 * @since 1.0.0
 */
final readonly class StaleWhileRevalidate extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the revalidation window.
     *
     * @param non-negative-int $seconds <p>
     * Maximum number of seconds during which a stale response may be served while revalidation occurs.
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
                'Cache-Control stale-while-revalidate cannot be negative.'
            )
        );

        parent::__construct(
            new Name('stale-while-revalidate'),
            new Value((string)$seconds)
        );

    }

    /**
     * ### Parses a Stale-While-Revalidate Directive
     *
     * Parses the stale-while-revalidate directive and validates its delta-seconds value.
     *
     * Both token and quoted-string representations are accepted. Leading zeros are permitted and normalized.
     *
     * <code>
     * $directive = StaleWhileRevalidate::parse(
     *     'stale-while-revalidate=600'
     * );
     *
     * $directive->seconds(); // 600
     * $directive->value();   // stale-while-revalidate=600
     *
     * $directive = StaleWhileRevalidate::parse(
     *     'STALE-WHILE-REVALIDATE="000600"'
     * );
     *
     * $directive->seconds(); // 600
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
     * Serialized stale-while-revalidate directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed stale-while-revalidate directive.
     */
    public static function parse (string $value):self {

        $parameter = Parameter\Quoted::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(
            new Name('stale-while-revalidate')
        )) throw new InvalidParameterException(
            'Expected the Cache-Control stale-while-revalidate directive.'
        );

        $seconds = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $seconds))
            throw new InvalidParameterException(
                'Cache-Control stale-while-revalidate must contain decimal digits.'
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
            'Cache-Control stale-while-revalidate exceeds the supported integer range.'
        );

        /** @var non-negative-int $normalized */
        $normalized = (int)$normalized;

        return new self($normalized);

    }

    /**
     * ### Gets the Stale-While-Revalidate Window
     *
     * Returns the maximum number of seconds during which a cache may serve a stale response while revalidating it.
     *
     * <code>
     * $directive = new StaleWhileRevalidate(600);
     *
     * $directive->seconds(); // 600
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-negative-int Revalidation window in seconds.
     */
    public function seconds ():int {

        return $this->seconds;

    }

}