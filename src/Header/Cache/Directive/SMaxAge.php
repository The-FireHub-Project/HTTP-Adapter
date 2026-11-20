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
 * ### Cache-Control S-MaxAge Directive
 *
 * Represents the s-maxage cache directive.
 *
 * Specifies the maximum age, in seconds, that a response may be considered fresh by a shared cache.
 *
 * This directive applies only to shared caches, such as proxy caches and content delivery networks.
 *
 * When present, s-maxage overrides max-age and Expires for shared caches.
 *
 * The value must be a non-negative integer.
 *
 * <code>
 * $directive = new SMaxAge(3600);
 *
 * $directive->seconds(); // 3600
 * $directive->value();   // s-maxage=3600
 * </code>
 *
 * @since 1.0.0
 */
final readonly class SMaxAge extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the maximum age.
     *
     * @param non-negative-int $seconds <p>
     * Maximum age in seconds for shared caches.
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
                'Cache-Control s-maxage cannot be negative.'
            )
        );

        parent::__construct(
            new Name('s-maxage'),
            new Value((string)$seconds)
        );

    }

    /**
     * ### Parses an S-MaxAge Directive
     *
     * Parses the s-maxage directive and validates its numeric value.
     *
     * Both token and quoted-string representations are accepted. Leading zeros are permitted and normalized.
     *
     * <code>
     * $directive = SMaxAge::parse('s-maxage=3600');
     *
     * $directive->seconds(); // 3600
     * $directive->value();   // s-maxage=3600
     *
     * $directive = SMaxAge::parse('S-MAXAGE="0003600"');
     *
     * $directive->seconds(); // 3600
     * $directive->value();   // s-maxage=3600
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To parse token or quoted-string parameter syntax.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare the parameter name.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To validate the integer range.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the numeric value.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove leading zeros.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To validate the integer length.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the integer value.
     *
     * @param non-empty-string $value <p>
     * Serialized s-maxage directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed s-maxage directive.
     */
    public static function parse (string $value):self {

        $parameter = Parameter\Quoted::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('s-maxage')))
            throw new InvalidParameterException(
                'Expected the Cache-Control s-maxage directive.'
            );

        $seconds = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $seconds))
            throw new InvalidParameterException(
                'Cache-Control s-maxage must contain decimal digits.'
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
            'Cache-Control s-maxage exceeds the supported integer range.'
        );

        /** @var non-negative-int $normalized */
        $normalized = (int)$normalized;

        return new self($normalized);

    }

    /**
     * ### Gets the Maximum Age for Shared Caches
     *
     * Returns the maximum cache age in seconds.
     *
     * <code>
     * $directive = new SMaxAge(3600);
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