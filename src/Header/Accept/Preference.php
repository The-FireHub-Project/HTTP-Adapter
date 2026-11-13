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

namespace FireHub\Adapter\HTTP\Header\Accept;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ {
    Media\Range, Parameter\Quality,
    Name, Parameter, Parameters
};
use FireHub\Adapter\HTTP\Header\Exception\ {
    InvalidMediaTypeException, InvalidParameterException
};
use FireHub\Runtime;

/**
 * ### HTTP Accept Preference
 *
 * Represents a single media range preference within an HTTP Accept header.
 *
 * A preference consists of a media range, optional media parameters, and an optional quality parameter.
 *
 * Media parameters must precede the quality parameter. When present, the quality parameter is always serialized last.
 *
 * An omitted quality parameter has an effective quality value of 1.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Accept\Preference;
 * use FireHub\Adapter\HTTP\Header\Media\Range;
 * use FireHub\Adapter\HTTP\Header\Parameter\Quality;
 *
 * $preference = new Preference(
 *     Range::parse('text/html'),
 *     Quality::of('0.8')
 * );
 *
 * $preference->value(); // text/html; q=0.8
 *
 * $preference = Preference::parse(
 *     'application/json; profile="example"; q=0.9'
 * );
 *
 * $preference->range()->value(); // application/json
 * $preference->quality(); // null
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Preference extends ValueObject {

    /**
     * ### Media parameters associated with the preference
     * @since 1.0.0
     */
    private Parameters $parameters;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() Checks if the condition is met.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::has() Checks if the media parameters contain a quality parameter.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Range $range <p>
     * Media range accepted by the client.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Parameter\Quality $quality <p>
     * Optional quality parameter, or null when omitted.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Parameter ...$parameters <p>
     * Media parameters associated with the range.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the media parameters contain a
     * quality parameter or duplicate parameter names.s
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private Range $range,
        private ?Quality $quality = null,
        Parameter ...$parameters
    ) {

        $this->parameters = new Parameters(...$parameters);

        $this->guard(
            fn() => !$this->parameters->has(new Name('q')),
            fn() => new InvalidParameterException(
                'The quality parameter must be provided separately.'
            )
        );

    }

    /**
     * ### Parses an HTTP Accept Preference
     *
     * Parses a media range preference containing optional media parameters and an optional quality parameter.
     *
     * The quality parameter must appear after all media parameters. No additional parameters are permitted after the
     * quality parameter.
     *
     * <code>
     * $preference = Preference::parse(
     *     'text/html; level=1; q=0.8'
     * );
     *
     * $preference->range()->value(); // text/html
     * $preference->parameters()->count(); // 1
     * $preference->quality()?->content()->value(); // 0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Range::parse() To parse the media range.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::parse() To parse the parameter collection.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::all() To iterate over the parameter collection.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To compare parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::of() To parse the quality parameter.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the separator between the media range and parameters.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the media range and parameters from the string.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP Accept preference.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media range is invalid.
     * @@throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the media parameters contain a
     * quality parameter or duplicate parameter names.s
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP Accept preference.
     */
    public static function parse (string $value):self {

        $separator = Runtime\Str\SB\Search::firstPosition(';', $value);

        if ($separator === false)
            return new self(Range::parse($value));

        /** @var non-empty-string $media_type */
        $media_type = Runtime\Str\SB\Access::part($value, 0, $separator);

        $range = Range::parse($media_type);

        $parsed = Parameters::parse(
            Runtime\Str\SB\Access::part($value, $separator + 1)
        );

        $parameters = []; $quality = null;
        foreach ($parsed->all() as $parameter) {

            if ($parameter->name()->equals(new Name('q'))) {

                $quality = Quality::of($parameter->content()->value());

                continue;

            }

            if ($quality !== null)
                throw new InvalidParameterException(
                    'No parameters are allowed after the quality parameter.'
                );

            $parameters[] = $parameter;

        }

        return new self(
            $range,
            $quality,
            ...$parameters
        );

    }

    /**
     * ### Get Media Range
     *
     * Returns the media range associated with this preference.
     *
     * <code>
     * $preference = Preference::parse('text/html; q=0.8');
     *
     * $preference->range()->value(); // text/html
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Media\Range Media range.
     */
    public function range ():Range {

        return $this->range;

    }

    /**
     * ### Get Quality
     *
     * Returns the explicitly specified quality parameter.
     *
     * If no quality parameter was specified, null is returned.
     *
     * <code>
     * $preference = Preference::parse('text/html; q=0.8');
     *
     * $preference->quality()?->content()->value(); // 0.8
     *
     * $preference = Preference::parse('application/json');
     *
     * $preference->quality(); // null
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
     * ### Get Media Parameters
     *
     * Returns the media parameters associated with this preference.
     *
     * The quality parameter is excluded from the collection.
     *
     * <code>
     * $preference = Preference::parse(
     *     'text/html; level=1; q=0.8'
     * );
     *
     * $preference->parameters()->count(); // 1
     * $preference->parameters()->value(); // level=1
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Parameters Media parameters.
     */
    public function parameters ():Parameters {

        return $this->parameters;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $preference = Preference::parse(
     *     'text/html; level=1; q=0.8'
     * );
     *
     * $preference->value(); // text/html; level=1; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Range::value() To serialize the media range.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::value() To serialize the media parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::value() To serialize the quality parameter.
     */
    public function value ():string {

        $value = $this->range->value();

        if ($this->parameters->count() > 0)
            $value .= '; ' . $this->parameters->value();

        if ($this->quality !== null)
            $value .= '; ' . $this->quality->value();

        return $value;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{
     *     \FireHub\Adapter\HTTP\Header\Media\Range,
     *     \FireHub\Adapter\HTTP\Header\Parameters,
     *     int
     * } Normalized preference components.
     */
    protected function comparisonValue ():array {

        return [
            $this->range,
            $this->parameters,
            $this->quality !== null
                ? (int) ((float) $this->quality->content()->value() * 1000)
                : 1000
        ];

    }

}