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
    Disposition\KnownType, Disposition\Type,
    Parsing\Splitter,
    Parameter, Parameters
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Disposition Header
 *
 * Represents a Content-Disposition header field value.
 *
 * Supports standard and custom disposition types with optional parameters.
 *
 * <code>
 * $disposition = Disposition::known(
 *     KnownType::ATTACHMENT,
 *     Parameter::parse('filename="report.pdf"')
 * );
 *
 * $disposition->value();
 * // attachment; filename=report.pdf
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Disposition extends ValueObject {

    /**
     * ### Disposition parameters
     * @since 1.0.0
     */
    private Parameters $parameters;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Disposition\Type $type <p>
     * Disposition type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Parameter ...$parameters <p>
     * Disposition parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If duplicate parameter names are
     * provided.
     *
     * @return void
     */
    public function __construct (
        private Type $type,
        Parameter ...$parameters
    ) {

        $this->parameters = new Parameters(...$parameters);

    }

    /**
     * ### Creates a Known Content-Disposition
     *
     * Creates a Content-Disposition value using a predefined disposition type and optional parameters.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Content\Disposition;
     * use FireHub\Adapter\HTTP\Header\Disposition\KnownType;
     *
     * $disposition = Disposition::known(KnownType::INLINE);
     *
     * $disposition->value();
     * // inline
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Disposition\Type::known() To create the disposition type.
     *
     * @param \FireHub\Adapter\HTTP\Header\Disposition\KnownType $type <p>
     * Known disposition type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Parameter ...$parameters <p>
     * Optional disposition parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the disposition type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Content-Disposition value object.
     */
    public static function known (KnownType $type, Parameter ...$parameters):self {

        return new self(
            Type::known($type),
            ...$parameters
        );

    }

    /**
     * ### Parses a Content-Disposition Header
     *
     * Parses a serialized Content-Disposition field value into a disposition type and its optional parameters.
     *
     * Semicolons inside quoted parameter values are preserved.
     *
     * Empty parameter segments are rejected.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Content\Disposition;
     *
     * $disposition = Disposition::parse(
     *     'attachment; filename="report.pdf"'
     * );
     *
     * $disposition->type()->value();
     * // attachment
     *
     * $disposition->parameters()->count();
     * // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To split the field value while respecting quoted
     * strings.
     * @uses \FireHub\Adapter\HTTP\Header\Disposition\Type::parse() To parse the disposition type.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse individual disposition parameters.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove leading and trailing whitespace.
     * @uses \FireHub\Runtime\Arr\Structure::slice() To remove the first element of an array.
     *
     * @param non-empty-string $value <p>
     * Serialized Content-Disposition header field value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the disposition type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Content-Disposition value object.
     */
    public static function parse (string $value):self {

        $parts = Splitter::split($value, ';');

        $type = Runtime\Str\SB\Transform::trim(
            Runtime\Arr\Access::first($parts) ?? ''
        );

        if ($type === '')
            throw new InvalidFieldValueException(
                'Content-Disposition type cannot be empty.'
            );

        $type = Type::parse($type);

        $parameters = [];

        foreach (Runtime\Arr\Structure::slice($parts, 1) as $part) {

            $part = Runtime\Str\SB\Transform::trim($part);

            if ($part === '')
                throw new InvalidFieldValueException(
                    'Invalid empty Content-Disposition parameter.'
                );

            $parameters[] = Parameter::parse($part);

        }

        return new self($type, ...$parameters);

    }

    /**
     * ### Gets the Disposition Type
     *
     * Returns the disposition type associated with this header.
     *
     * <code>
     * $disposition = Disposition::known(KnownType::ATTACHMENT);
     *
     * $disposition->type()->value();
     * // attachment
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Disposition\Type Disposition type.
     */
    public function type ():Type {

        return $this->type;

    }

    /**
     * ### Gets the Disposition Parameters
     *
     * Returns the immutable collection of disposition parameters.
     *
     * <code>
     * $disposition = Disposition::parse(
     *     'form-data; name="avatar"; filename="photo.jpg"'
     * );
     *
     * $disposition->parameters()->count();
     * // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Parameters Disposition parameters.
     */
    public function parameters ():Parameters {

        return $this->parameters;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disposition = Disposition::known(
     *     KnownType::ATTACHMENT,
     *     Parameter::parse('filename="report.pdf"')
     * );
     *
     * $disposition->value();
     * // attachment; filename=report.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::value() To serialize the disposition parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::count() To check if there are parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Disposition\Type::value() To serialize the disposition type.
     */
    public function value ():string {

        $value = $this->type->value();

        if ($this->parameters->count() === 0)
            return $value;

        return $value.'; '.$this->parameters->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the disposition type.
     * @uses \FireHub\Adapter\HTTP\Header\Parameters::value() To serialize the disposition parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Disposition\Type::value() To serialize the disposition type.
     *
     * @return array{non-empty-string, string} Normalized comparison value.
     */
    protected function comparisonValue ():array {

        /** @var lowercase-string&non-empty-string $value */
        $value = Runtime\Str\SB\Casing::toLower($this->type->value());

        return [
            $value,
            $this->parameters->value()
        ];

    }

}