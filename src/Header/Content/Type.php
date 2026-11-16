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

namespace FireHub\Adapter\HTTP\Header\Content;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ {
    Media\Parameter\Boundary, Media\KnownMediaType, Media\MediaType,
    Parameter\Charset,
    Parsing\Splitter,
    Name, Parameter, Parameters,
};
use FireHub\Adapter\HTTP\Header\Exception\ {
    InvalidFieldValueException,
    InvalidParameterException
};
use FireHub\Runtime;

/**
 * ### HTTP Content-Type Header
 *
 * Represents the media type of HTTP representation and its parameters.
 *
 * The media type must be concrete and cannot contain wildcards. Parameters are stored as an immutable collection.
 *
 * Known charset and multipart boundary parameters are validated using their specialized value objects.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Content\Type;
 * use FireHub\Adapter\HTTP\Header\Media\KnownMediaType;
 * use FireHub\Adapter\HTTP\Header\Parameter\Charset;
 * use FireHub\Core\Type\Str\Encoding;
 *
 * $type = Type::known(
 *     KnownMediaType::TEXT_HTML,
 *     Charset::of(Encoding::UTF_8)
 * );
 *
 * $type->value();
 * // text/html; charset=UTF-8
 *
 * $type = Type::parse(
 *     'multipart/form-data; boundary=FireHub123'
 * );
 *
 * $type->mediaType()->value();
 * // multipart/form-data
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Type extends ValueObject {

    /**
     * ### Content-Type parameters
     * @since 1.0.0
     */
    private Parameters $parameters;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::validateParameter() To validate parameters.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\MediaType $media_type <p>
     * Concrete media type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Parameter ...$parameters <p>
     * Media type parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private MediaType $media_type,
        Parameter ...$parameters
    ) {

        $validated = [];

        foreach ($parameters as $parameter)
            $validated[] = self::validateParameter($parameter);

        $this->parameters = new Parameters(...$validated);

    }

    /**
     * ### Creates a Content-Type from a Known Media Type
     *
     * <code>
     * $type = Type::known(
     *     KnownMediaType::APPLICATION_JSON
     * );
     *
     * $type->value(); // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\MediaType::known() To create the media type.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\KnownMediaType $media_type <p>
     * Predefined media type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Parameter ...$parameters <p>
     * Optional media type parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Content-Type value object.
     */
    public static function known (KnownMediaType $media_type, Parameter ...$parameters):self {

        return new self(
            MediaType::known($media_type),
            ...$parameters
        );

    }

    /**
     * ### Parses an HTTP Content-Type Header
     *
     * Parses a concrete media type followed by optional parameters.
     *
     * Quoted semicolons are preserved.
     *
     * <code>
     * $type = Type::parse(
     *     'text/html; charset=utf-8'
     * );
     *
     * $type->value(); // text/html; charset=UTF-8
     *
     * $type = Type::parse(
     *     'multipart/form-data; boundary="example:123"'
     * );
     *
     * $type->value();
     * // multipart/form-data; boundary="example:123"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To split header components.
     * @uses \FireHub\Adapter\HTTP\Header\Media\MediaType::parse() To parse the media type.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse a media parameter.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove leading and trailing whitespace.
     * @uses \FireHub\Runtime\Arr\Access::first() To get the first element of an array.
     *
     * @param string $value <p>
     * Serialized Content-Type header.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Content-Type.
     */
    public static function parse (string $value):self {

        $parts = Splitter::split($value, ';');

        /** @var string $first_part */
        $first_part = Runtime\Arr\Access::first($parts);

        /** @var non-empty-string $media */
        $media = Runtime\Str\SB\Transform::trim(
            $first_part,
            characters: " \t"
        );

        if ($media === '')
            throw new InvalidFieldValueException(
                'HTTP Content-Type cannot be empty.'
            );

        $media_type = MediaType::parse($media);

        $parameters = [];

        foreach ($parts as $index => $part) {

            if ($index === 0) continue;

            $part = Runtime\Str\SB\Transform::trim(
                $part,
                characters: " \t"
            );

            if ($part === '')
                throw new InvalidParameterException(
                    'Empty HTTP Content-Type parameter.'
                );

            $parameters[] = Parameter::parse($part);

        }

        return new self(
            $media_type,
            ...$parameters
        );

    }

    /**
     * ### Gets the Media Type
     *
     * <code>
     * $type = Type::parse('application/json');
     *
     * $type->mediaType()->value();
     * // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Media\MediaType Media type.
     */
    public function mediaType ():MediaType {

        return $this->media_type;

    }

    /**
     * ### Gets the Content-Type Parameters
     *
     * <code>
     * $type = Type::parse(
     *     'text/html; charset=UTF-8'
     * );
     *
     * $type->parameters()->count(); // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Parameters Parameters.
     */
    public function parameters ():Parameters {

        return $this->parameters;

    }

    /**
     * ### Gets the Charset Parameter
     *
     * Returns null when no charset parameter exists.
     *
     * <code>
     * $type = Type::parse(
     *     'text/html; charset=UTF-8'
     * );
     *
     * $type->charset()?->encoding();
     * // Encoding::UTF_8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Charset::parse() To parse the charset parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::get() To get the parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::value() To get the parameter value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Parameter\Charset Charset parameter.
     */
    public function charset ():?Charset {

        $parameter = $this->parameters->get(new Name('charset'));

        if ($parameter === null) return null;

        return $parameter instanceof Charset
            ? $parameter
            : Charset::parse($parameter->value());

    }

    /**
     * ### Gets the Multipart Boundary Parameter
     *
     * Returns null when no boundary parameter exists.
     *
     * <code>
     * $type = Type::parse(
     *     'multipart/form-data; boundary=FireHub123'
     * );
     *
     * $type->boundary()?->boundary();
     * // FireHub123
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter\Boundary::parse() To parse the boundary parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::get() To get the parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::value() To get the parameter value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Media\Parameter\Boundary Boundary parameter.
     */
    public function boundary ():?Boundary {

        $parameter = $this->parameters->get(new Name('boundary'));

        if ($parameter === null) return null;

        return $parameter instanceof Boundary
            ? $parameter
            : Boundary::parse($parameter->value());

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $type = Type::parse(
     *     'text/html; charset=UTF-8'
     * );
     *
     * $type->value();
     * // text/html; charset=UTF-8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\MediaType::value() To serialize the media type.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::value() To serialize the media parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::count() To check if there are parameters.
     */
    public function value ():string {

        $value = $this->media_type->value();

        if ($this->parameters->count() === 0)
            return $value;

        return $value . '; ' . $this->parameters->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the media type.
     * @uses \FireHub\Adapter\HTTP\Header\Media\MediaType::value() To get the media type.
     *
     * @return array{string, \FireHub\Adapter\HTTP\Header\Parameters} Normalized components.
     */
    protected function comparisonValue ():array {

        return [
            Runtime\Str\SB\Casing::toLower($this->media_type->value()),
            $this->parameters
        ];

    }

    /**
     * ### Validates a Content-Type Parameter
     *
     * Converts recognized parameters into specialized value objects.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Charset::parse() To parse a charset parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter\Boundary::parse() To parse a boundary parameter.
     *
     * @param \FireHub\Adapter\HTTP\Header\Parameter $parameter <p>
     * Parameter to validate.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Parameter Validated parameter.
     */
    private static function validateParameter (Parameter $parameter):Parameter {

        if ($parameter->name()->equals(new Name('charset')))
            return Charset::parse($parameter->value());

        if ($parameter->name()->equals(new Name('boundary')))
            return Boundary::parse($parameter->value());

        return $parameter;

    }

}