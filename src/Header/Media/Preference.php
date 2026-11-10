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

namespace FireHub\Adapter\HTTP\Header\Media;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Quality;
use FireHub\Adapter\HTTP\Header\Parameter\Parser;
use FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP media preference
 *
 * Encapsulates a concrete media type or wildcard media range together with its associated quality factor.
 *
 * Media preferences are used in HTTP content negotiation to express which representations a client accepts
 * and their relative priorities.
 *
 * The quality factor ranges from <code>0</code> to <code>1</code>, with a maximum precision of three decimal places.
 * A quality factor of <code>1</code> represents the highest preference, while <code>0</code> indicates that the
 * representation is not acceptable.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Preference extends ValueObject {

    /**
     * ### Media range parameters
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string>>
     */
    private array $parameters;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the media range parameter names to lowercase.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter::parameterName() To get the parameter name.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate media range parameters.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Range|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string> $media <p>
     * Accepted media type or media range.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Quality $quality [optional] <p>
     * Optional quality factor. Defaults to the maximum preference.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string> ...$parameters [optional] <p>
     * Media range parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media type is invalid.
     *
     * @return void
     */
    public function __construct (
        private Type|Range|Custom $media,
        private Quality $quality = new Quality(1.0),
        Parameter ...$parameters
    ) {

        $names = [];

        foreach ($parameters as $parameter) {

            $name = Runtime\Str\SB\Casing::toLower($parameter->parameterName());

            if ($name === 'q')
                throw new InvalidMediaTypeException(
                    'The q parameter is reserved for HTTP quality.'
                );

            if (isset($names[$name]))
                throw new InvalidMediaTypeException(
                    "Duplicate Accept media parameter: $name."
                );

            $names[$name] = true;

        }

        $this->parameters = Runtime\Arr\Access::values($parameters);

    }

    /**
     * ### Parses an HTTP Accept media preference
     *
     * Converts a raw media preference into its media type, optional parameters, and quality factor.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Parser::parse() To parse HTTP parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Quality::parse() To parse the quality factor.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize media types and parameter names.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the position of the first ';' character.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the media type and parameters.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the parameter value.
     *
     * @param string $value <p>
     * Raw Accept media preference.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media preference is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If parameter syntax is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaPreferenceExceptionQuality If quality is invalid.
     *
     * @return self<non-empty-string> Parsed media preference.
     */
    public static function parse (string $value):self {

        $separator = Runtime\Str\SB\Search::firstPosition(';', $value);

        $media = Runtime\Str\SB\Transform::trim(
            $separator === false
                ? $value
                : Runtime\Str\SB\Access::part($value, 0, $separator),
            characters: " \t"
        );

        if ($media === '')
            throw new InvalidMediaTypeException(
                'Accept media type cannot be empty.'
            );

        $normalized = Runtime\Str\SB\Casing::toLower($media);

        /** @var non-empty-string&lowercase-string $normalized */
        $type = Range::tryFrom($normalized)
            ?? Type::tryFrom($normalized)
            ?? new Custom($normalized);

        if ($separator === false)
            return new self($type);

        $raw = Runtime\Str\SB\Access::part($value, $separator + 1);

        if (Runtime\Str\SB\Transform::trim($raw, characters: " \t") === '')
            throw new InvalidMediaTypeException(
                'Accept parameter list cannot be empty.'
            );

        $quality = new Quality(1.0);
        $parameters = [];
        $quality_found = false;

        foreach (Parser::parse($raw) as $parameter) {

            $name = Runtime\Str\SB\Casing::toLower(
                $parameter['name']
            );

            if ($name === 'q') {

                if ($quality_found)
                    throw new InvalidMediaTypeException(
                        'Duplicate Accept quality parameter.'
                    );

                $quality_found = true;

                $raw_quality = $parameter['value'];

                $quality = Quality::parse($raw_quality);

                continue;

            }

            if ($quality_found)
                throw new InvalidMediaTypeException(
                    'Accept extensions after q are not supported.'
                );

            $parameters[] = new Parameter(
                $parameter['name'],
                $parameter['value']
            );

        }

        return new self($type, $quality, ...$parameters);

    }

    /**
     * ### Gets the accepted media type or range
     *
     * <code>
     * $preference = new MediaPreference(MediaType::APPLICATION_JSON);
     *
     * $media = $preference->media();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Range|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string>
     * Accepted media type or media range.
     */
    public function media ():Type|Range|Custom {

        return $this->media;

    }

    /**
     * ### Gets the HTTP quality factor
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Quality Quality factor associated with this media preference.
     */
    public function quality ():Quality {

        return $this->quality;

    }

    /**
     * ### Gets the media range parameters
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string>> Media range parameters.
     */
    public function parameters ():array {

        return $this->parameters;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter::value() To serialize media parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Custom::value() To get the custom media type.
     */
    public function value ():string {

        $media = $this->media instanceof Custom
            ? $this->media->value()
            : $this->media->value;

        foreach ($this->parameters as $parameter)
            $media .= ';'.$parameter->value();

        $quality = $this->quality->value();

        if ($quality === 1.0) return $media;

        return "$media;q=$quality";

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Custom::value() To get the custom media type.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the media type to lowercase.
     * @uses \FireHub\Runtime\Arr\Transform::map() To serialize media parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter::parameterName() To get the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter::parameterValue() To get the parameter value.
     *
     * @return array{lowercase-string, float, list<array{lowercase-string, string}>} Comparison value.
     */
    protected function comparisonValue ():array {

        $media = $this->media instanceof Custom
            ? $this->media->value()
            : $this->media->value;

        return [
            Runtime\Str\SB\Casing::toLower($media),
            $this->quality->value(),
            Runtime\Arr\Transform::map(
                $this->parameters,
                static fn(Parameter $parameter):array => [
                    Runtime\Str\SB\Casing::toLower(
                        $parameter->parameterName()
                    ),
                    $parameter->parameterValue()
                ]
            )
        ];

    }

}