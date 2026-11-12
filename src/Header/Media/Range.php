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
use FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException;
use FireHub\Runtime;

/**
 * ### HTTP Media Range
 *
 * Represents a media range used in HTTP content negotiation.
 *
 * A media range identifies one or more acceptable media types and supports three forms: a specific media type, all
 * subtypes of a primary type, or all media types.
 *
 * Supported forms:
 *
 * - application/json
 * - application/*
 * - *\/*
 *
 * Media ranges are case-insensitive. Original casing is preserved for serialization, while equality comparisons use
 * normalized values.
 *
 * This class represents only the type/subtype range. Media parameters and quality preferences are managed separately.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Media\ {
 *     Range, Type, Subtype
 * };
 *
 * $range = new Range(
 *     new Type('application'),
 *     new Subtype('json')
 * );
 *
 * $range->value(); // application/json
 *
 * $range = new Range(new Type('application'));
 *
 * $range->value(); // application/*
 *
 * $range = new Range;
 *
 * $range->value(); // *\/*
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Range extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param null|\FireHub\Adapter\HTTP\Header\Media\Type $type <p>
     * Primary MIME media type, or null to match all types.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Media\Subtype $subtype <p>
     * MIME media subtype, or null to match all subtypes.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If a subtype is provided without a primary type.
     *
     * @return void
     */
    public function __construct (
        private ?Type $type = null,
        private ?Subtype $subtype = null
    ) {

        if ($this->type === null && $this->subtype !== null)
            throw new InvalidMediaTypeException(
                'A media range cannot specify a subtype without a primary type.'
            );

    }

    /**
     * ### Parses a serialized media range
     *
     * Converts a media range string into structured primary type and subtype components.
     *
     * <code>
     * $range = Range::parse('application/json');
     *
     * $range->value(); // application/json
     *
     * $range = Range::parse('application/*');
     *
     * $range->value(); // application/*
     *
     * $range = Range::parse('*' . '/*');
     *
     * $range->value(); // *\/*
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the media range.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To validate the number of parts.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP media range.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     *
     * @return self Parsed media range.
     */
    public static function parse (string $value):self {

        if ($value === '*/*') return new self;

        /** @var array{0: non-empty-string, 1: non-empty-string} $parts */
        $parts = Runtime\Str\SB\Delimiter::explode($value, '/', 2);

        if (Runtime\Arr\Inspection::count($parts) !== 2 || $parts[0] === '' || $parts[1] === '')
            throw new InvalidMediaTypeException(
                'Invalid HTTP media range.'
            );

        if ($parts[0] === '*')
            throw new InvalidMediaTypeException(
                'A wildcard primary type requires a wildcard subtype.'
            );

        return new self(
            new Type($parts[0]),
            $parts[1] === '*' ? null : new Subtype($parts[1])
        );

    }

    /**
     * ### Returns the primary media type
     *
     * Returns null when the range matches all primary media types.
     *
     * <code>
     * $range = Range::parse('application/*');
     *
     * $range->type()?->value(); // application
     *
     * $range = Range::parse('*' . '/*');
     *
     * $range->type(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Media\Type Primary media type.
     */
    public function type ():?Type {

        return $this->type;

    }

    /**
     * ### Returns the media subtype
     *
     * Returns null when the range matches all subtypes.
     *
     * <code>
     * $range = Range::parse('application/json');
     *
     * $range->subtype()?->value(); // json
     *
     * $range = Range::parse('application/*');
     *
     * $range->subtype(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Media\Subtype Media subtype.
     */
    public function subtype ():?Subtype {

        return $this->subtype;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $range = new Range(
     *     new Type('application'),
     *     new Subtype('json')
     * );
     *
     * $range->value(); // application/json
     *
     * $range = new Range(
     *     new Type('application')
     * );
     *
     * $range->value(); // application/*
     *
     * $range = new Range;
     *
     * $range->value(); // *\/*
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Type::value() To serialize the primary type.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Subtype::value() To serialize the subtype.
     *
     * @return non-empty-string Serialized HTTP media range.
     */
    public function value ():string {

        return ($this->type?->value() ?? '*')
            . '/'
            . ($this->subtype?->value() ?? '*');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the media range.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->value());

    }

}