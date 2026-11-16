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

namespace FireHub\Adapter\HTTP\Header\Accept\Encoding;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ {
    Coding\Content, Parameter\Quality
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Accept-Encoding Preference
 *
 * Represents a known content coding or the wildcard with an optional quality parameter.
 *
 * An omitted quality parameter has an effective weight of 1000. An explicitly supplied quality parameter is
 * preserved during serialization.
 *
 * <code>
 * $preference = Preference::of(Content::BR, Quality::of('0.8'));
 *
 * $preference->value();  // br; q=0.8
 * $preference->weight(); // 800
 *
 * Preference::any()->value(); // *
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
     * @param null|\FireHub\Adapter\HTTP\Header\Coding\Content $coding <p>
     * Known content coding, or null for the wildcard.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Parameter\Quality $quality [optional] <p>
     * Explicit quality parameter, or null to use the default weight.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private ?Content $coding,
        private ?Quality $quality = null
    ) {}

    /**
     * ### Creates a Content Coding Preference
     *
     * <code>
     * $preference = Preference::of(Content::ZSTD, Quality::of('0.9'));
     *
     * $preference->value(); // zstd; q=0.9
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Coding\Content $coding <p>
     * Known content coding.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Parameter\Quality $quality [optional] <p>
     * Explicit quality parameter, or null to use the default weight.
     * </p>
     *
     * @return self New content coding preference.
     */
    public static function of (Content $coding, ?Quality $quality = null):self {

        return new self($coding, $quality);

    }

    /**
     * ### Creates a Wildcard Content Coding Preference
     *
     * Represents any available content coding not explicitly listed in the field.
     *
     * <code>
     * Preference::any()->value();                   // *
     * Preference::any(Quality::of('0.2'))->value(); // *; q=0.2
     * </code>
     *
     * @since 1.0.0
     *
     * @param null|\FireHub\Adapter\HTTP\Header\Parameter\Quality $quality [optional] <p>
     * Explicit quality parameter, or null to use the default weight.
     * </p>
     *
     * @return self Wildcard content coding preference.
     */
    public static function any (?Quality $quality = null):self {

        return new self(null, $quality);

    }

    /**
     * ### Parses an HTTP Accept-Encoding Preference
     *
     * Parses a known content coding or wildcard with an optional quality parameter.
     *
     * <code>
     * $preference = Preference::parse('br;q=0.8');
     *
     * $preference->coding(); // Content::BR
     * $preference->weight(); // 800
     * $preference->value();  // br; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To separate the coding and quality parameter.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the content coding.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::parse() To parse the quality parameter.
     *
     * @param string $value <p>
     * Serialized HTTP Accept-Encoding preference.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the content coding is unsupported.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the quality parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed content coding preference.
     */
    public static function parse (string $value):self {

        $parts = Runtime\Str\SB\Delimiter::explode($value, ';', 2);

        $coding = Runtime\Str\SB\Casing::toLower(
            Runtime\Str\SB\Transform::trim($parts[0], characters: " \t")
        );

        $quality = null;
        if (isset($parts[1])) {

            $parameter = Runtime\Str\SB\Transform::trim(
                $parts[1],
                characters: " \t"
            );

            if ($parameter === '')
                throw new InvalidFieldValueException(
                    'Accept-Encoding quality parameter cannot be empty.'
                );

            $quality = Quality::parse($parameter);

        }

        if ($coding === '*') return new self(null, $quality);

        $content = Content::tryFrom($coding)
            ?? throw new InvalidFieldValueException(
                'Expected a known content coding or wildcard in Accept-Encoding.'
            );

        return new self($content, $quality);

    }

    /**
     * ### Gets the Content Coding
     *
     * <code>
     * Preference::of(Content::GZIP)->coding(); // Content::GZIP
     * Preference::any()->coding();             // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Coding\Content|null Known content coding, or null for the wildcard.
     */
    public function coding ():?Content {

        return $this->coding;

    }

    /**
     * ### Gets the Quality Parameter
     *
     * <code>
     * Preference::parse('br;q=0.8')->quality()?->value(); // q=0.8
     * Preference::parse('br')->quality();                 // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Parameter\Quality Explicit quality parameter, if supplied.
     */
    public function quality ():?Quality {

        return $this->quality;

    }

    /**
     * ### Gets the Effective Quality Weight
     *
     * Returns the normalized quality weight as an integer between 0 and 1000.
     *
     * <code>
     * Preference::parse('gzip')->weight();       // 1000
     * Preference::parse('br;q=0.75')->weight();  // 750
     * Preference::parse('*;q=0')->weight();      // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::weight() To normalize an explicit quality parameter.
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
     * Preference::of(Content::BR, Quality::of('0.8'))->value(); // br; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::value() To serialize the quality parameter.
     *
     * @return non-empty-string Serialized content coding preference.
     */
    public function value ():string {

        $coding = $this->coding === null
            ? '*'
            : $this->coding->value;

        return $this->quality === null
            ? $coding
            : $coding . '; ' . $this->quality->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::weight() To get the effective weight.
     *
     * @return array{\FireHub\Adapter\HTTP\Header\Coding\Content|null, int<0, 1000>} Coding and effective weight.
     */
    protected function comparisonValue ():array {

        return [
            $this->coding,
            $this->weight()
        ];

    }

}