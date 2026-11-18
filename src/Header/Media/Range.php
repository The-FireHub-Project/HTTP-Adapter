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

use FireHub\Adapter\HTTP\Header\Value;
use FireHub\Adapter\HTTP\Header\Media\Known\ {
    MediaType as KnownMediaType, Type as KnownType
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
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
 */
final readonly class Range extends Value {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the media range.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Range::value() To get the serialized media range.
     *
     * @param null|\FireHub\Adapter\HTTP\Header\Media\Type $type [optional] <p>
     * Primary MIME media type, or null to match all types.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Media\Subtype $subtype [optional] <p>
     * MIME media subtype, or null to match all subtypes.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a subtype is provided without a
     * primary type.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private ?Type $type = null,
        private ?Subtype $subtype = null
    ) {

        $this->guard(
            fn() => $this->type !== null || $this->subtype === null,
            fn() => new InvalidFieldValueException(
                'A media range cannot specify a subtype without a primary type.'
            )
        );

        parent::__construct($this->value());

    }

    /**
     * ### Creates a Wildcard Media Range
     *
     * Creates a media range matching any primary media type and any subtype.
     *
     * <code>
     * $range = Range::any();
     *
     * $range->value(); // * / * (without spaces)
     * </code>
     *
     * @since 1.0.0
     *
     * @return self Wildcard media range.
     */
    public static function any ():self {

        return new self;

    }

    /**
     * ### Creates a Range from a Known Media Type
     *
     * Creates an exact media range from a predefined MIME media type.
     *
     * <code>
     * $range = Range::known(KnownMediaType::APPLICATION_JSON);
     *
     * $range->value(); // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::parse() To parse the known media type.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Known\MediaType $media_type <p>
     * Predefined MIME media type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the media type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Exact media range.
     */
    public static function known (KnownMediaType $media_type):self {

        return self::parse($media_type->value);

    }

    /**
     * ### Creates a Range for a Primary Media Type
     *
     * Creates a wildcard media range matching all subtypes of the specified primary media type.
     *
     * <code>
     * $range = Range::knownType(KnownType::APPLICATION);
     *
     * $range->value(); // application/*
     *
     * $range = Range::knownType(KnownType::IMAGE);
     *
     * $range->value(); // image/*
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Type::known() To create the primary media type.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Known\Type $type <p>
     * Predefined primary media type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a subtype is provided without a
     * primary type.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Media range matching all subtypes.
     */
    public static function knownType (KnownType $type):self {

        return new self(Type::known($type));

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
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the media range is invalid.
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
            throw new InvalidFieldValueException(
                'Invalid HTTP media range.'
            );

        if ($parts[0] === '*')
            throw new InvalidFieldValueException(
                'A wildcard primary type requires a wildcard subtype.'
            );

        return new self(
            new Type($parts[0]),
            $parts[1] === '*' ? null : new Subtype($parts[1])
        );

    }

    /**
     * ### Checks whether a media type matches this range
     *
     * Determines whether the specified media type is included in the range.
     *
     * <code>
     * $range = Range::parse('image/*');
     *
     * $range->matches(MediaType::parse('image/png')); // true
     * $range->matches(MediaType::parse('text/html')); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Type::equals() To compare primary media types.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Subtype::equals() To compare media subtypes.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\MediaType $media <p>
     * Concrete media type to match against this range.
     * </p>
     *
     * @return bool True if the media type matches this range.
     */
    public function matches (MediaType $media):bool {

        if ($this->type === null)
            return true;

        if (!$this->type->equals($media->type()))
            return false;

        return $this->subtype === null
            || $this->subtype->equals($media->subtype());

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