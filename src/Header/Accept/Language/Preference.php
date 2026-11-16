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

namespace FireHub\Adapter\HTTP\Header\Accept\Language;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Parameter\Quality;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Accept-Language Preference
 *
 * Represents a language range with an optional quality parameter.
 *
 * An omitted quality parameter has an effective weight of 1000.
 *
 * Explicit quality parameters are preserved during serialization.
 *
 * <code>
 * $preference = Preference::of(
 *     Range::of('en-US'),
 *     Quality::of('0.8')
 * );
 *
 * $preference->value();  // en-US; q=0.8
 * $preference->weight(); // 800
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Preference extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Language\Range $range <p>
     * Preferred language range.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Parameter\Quality $quality [optional] <p>
     * Explicit quality parameter.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Range $range,
        private ?Quality $quality = null
    ) {}

    /**
     * ### Creates a Language Preference
     *
     * <code>
     * $preference = Preference::of(
     *     Range::of('hr-HR'),
     *     Quality::of('0.9')
     * );
     *
     * $preference->value(); // hr-HR; q=0.9
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Language\Range $range <p>
     * Preferred language range.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Parameter\Quality $quality [optional] <p>
     * Explicit quality parameter.
     * </p>
     *
     * @return self New language preference.
     */
    public static function of (Range $range, ?Quality $quality = null):self {

        return new self($range, $quality);

    }

    /**
     * ### Creates a Wildcard Language Preference
     *
     * <code>
     * Preference::any()->value(); // *
     *
     * Preference::any(
     *     Quality::of('0.2')
     * )->value(); // *; q=0.2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language\Range::any() To create the wildcard range.
     *
     * @param null|\FireHub\Adapter\HTTP\Header\Parameter\Quality $quality [optional] <p>
     * Explicit quality parameter.
     * </p>
     *
     * @return self Wildcard language preference.
     */
    public static function any (?Quality $quality = null):self {

        return new self(Range::any(), $quality);

    }

    /**
     * ### Parses an HTTP Accept-Language Preference
     *
     * Parses a language range with an optional quality parameter.
     *
     * <code>
     * $preference = Preference::parse('en-US;q=0.8');
     *
     * $preference->range()->value(); // en-US
     * $preference->weight();         // 800
     * $preference->value();          // en-US; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To separate the language range and quality.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language\Range::parse() To parse the language range.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::parse() To parse the quality parameter.
     *
     * @param string $value <p>
     * Serialized HTTP Accept-Language preference.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the preference is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the quality parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed language preference.
     */
    public static function parse (string $value):self {

        $parts = Runtime\Str\SB\Delimiter::explode($value, ';', 2);

        $range = Range::parse($parts[0]);

        $quality = null;

        if (isset($parts[1])) {

            $parameter = Runtime\Str\SB\Transform::trim(
                $parts[1],
                characters: " \t"
            );

            if ($parameter === '')
                throw new InvalidFieldValueException(
                    'Accept-Language quality parameter cannot be empty.'
                );

            $quality = Quality::parse($parameter);

        }

        return new self($range, $quality);

    }

    /**
     * ### Gets the Language Range
     *
     * <code>
     * Preference::of(
     *     Range::of('hr-HR')
     * )->range()->value(); // hr-HR
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Accept\Language\Range Language range.
     */
    public function range ():Range {

        return $this->range;

    }

    /**
     * ### Gets the Quality Parameter
     *
     * <code>
     * Preference::of(
     *     Range::of('en'),
     *     Quality::of('0.8')
     * )->quality()?->value(); // q=0.8
     *
     * Preference::of(
     *     Range::of('hr')
     * )->quality(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Parameter\Quality Explicit quality parameter.
     */
    public function quality ():?Quality {

        return $this->quality;

    }

    /**
     * ### Gets the Effective Quality Weight
     *
     * Returns the effective quality weight as an integer between 0 and 1000.
     *
     * <code>
     * Preference::of(Range::of('hr'))->weight(); // 1000
     *
     * Preference::of(
     *     Range::of('en'),
     *     Quality::of('0.8')
     * )->weight(); // 800
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::weight() To normalize the quality parameter.
     *
     * @return int<0, 1000> Effective quality weight.
     */
    public function weight ():int {

        return $this->quality?->weight() ?? 1000;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * Preference::of(
     *     Range::of('en-US'),
     *     Quality::of('0.8')
     * )->value(); // en-US; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language\Range::value() To serialize the language range.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::value() To serialize the quality parameter.
     */
    public function value ():string {

        $range = $this->range->value();

        return $this->quality === null
            ? $range
            : $range . '; ' . $this->quality->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{\FireHub\Adapter\HTTP\Header\Accept\Language\Range, int<0, 1000>} Language range and effective weight.
     */
    protected function comparisonValue ():array {

        return [
            $this->range,
            $this->weight()
        ];

    }

}