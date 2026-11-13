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

namespace FireHub\Adapter\HTTP\Header\Parameter;

use FireHub\Adapter\HTTP\Header\ {
    Name, Value, Parameter
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Quality Parameter
 *
 * Represents an HTTP quality parameter with the fixed name "q".
 *
 * Quality values range from 0 to 1 and support a maximum of three decimal places.
 *
 * The original quality representation is preserved.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Quality;
 *
 * $quality = Quality::of('0.8');
 *
 * $quality->name()->value();    // q
 * $quality->content()->value(); // 0.8
 * $quality->value();            // q=0.8
 *
 * $quality = Quality::parse('q=0.750');
 *
 * $quality->content()->value(); // 0.750
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Quality extends Parameter {

    /**
     * ### Creates an HTTP quality parameter
     *
     * Creates a quality parameter from a numeric string representation.
     *
     * <code>
     * $quality = Quality::of('0.8');
     *
     * $quality->value(); // q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the quality value.
     *
     * @param string $value <p>
     * HTTP quality value between 0 and 1, with at most three decimal places.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self New HTTP quality parameter.
     */
    public static function of (string $value):self {

        if (!Runtime\Str\SB\Regex::match(
            '/\A(?:0(?:\.[0-9]{0,3})?|1(?:\.0{0,3})?)\z/',
            $value
        )) throw new InvalidParameterException(
            'HTTP quality must be between 0 and 1 with at most three decimal places.'
        );

        return new self(
            new Name('q'),
            new Value($value)
        );

    }

    /**
     * ### Parses an HTTP quality parameter
     *
     * Parses a serialized HTTP quality parameter and validates its parameter name and quality value.
     *
     * <code>
     * $quality = Quality::parse('q=0.8');
     *
     * $quality->content()->value(); // 0.8
     * $quality->value();            // q=0.8
     *
     * $quality = Quality::parse('Q=0.500');
     *
     * $quality->value(); // q=0.500
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::of() To validate and create the quality parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic HTTP parameter.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP quality parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP quality parameter.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if ($parameter->name()->comparisonValue() !== 'q')
            throw new InvalidParameterException(
                'Expected an HTTP quality parameter named "q".'
            );

        return self::of($parameter->content()->value());

    }

    /**
     * ### Gets the Quality Weight
     *
     * Returns the normalized quality value as an integer between 0 and 1000.
     *
     * <code>
     * $preference = Preference::parse('text/html');
     * $preference->weight(); // 1000
     *
     * $preference = Preference::parse('application/json;q=0.8');
     * $preference->weight(); // 800
     *
     * $preference = Preference::parse('image/png;q=0');
     * $preference->weight(); // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::content() To get the quality value.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the quality value into integer and fractional parts.
     * @uses \FireHub\Runtime\Str\SB\Transform::pad() To pad the fractional part with zeros.
     *
     * @return int<0, 1000> Normalized quality weight.
     */
    public function weight ():int {

        $value = $this->content()->value();

        /** @var array{numeric-string, numeric-string} $parts */
        $parts = Runtime\Str\SB\Delimiter::explode($value, '.', 2);

        $integer = (int)$parts[0];

        $fraction = $parts[1] ?? '';

        /** @var int<0, 1000> */
        return $integer * 1000
            + (int)Runtime\Str\SB\Transform::pad($fraction, 3, '0');

    }

}