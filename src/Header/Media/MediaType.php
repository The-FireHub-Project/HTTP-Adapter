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
 * ### HTTP Media Type
 *
 * Represents a complete MIME media type composed of a primary type and a subtype.
 *
 * Media types identify the format of a representation, such as application/json, text/html, or image/png.
 *
 * Both components are represented by immutable value objects.
 *
 * Media types are case-insensitive. Their original casing is preserved for serialization, while equality comparisons
 * use normalized values.
 *
 * This class does not support wildcard media ranges or media parameters.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Media\ {
 *     MediaType, Type, Subtype
 * };
 *
 * $media = new MediaType(
 *     new Type('application'),
 *     new Subtype('json')
 * );
 *
 * $media->value(); // application/json
 *
 * $media = MediaType::parse('text/html');
 *
 * $media->type()->value();    // text
 * $media->subtype()->value(); // html
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class MediaType extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Type $type <p>
     * Primary MIME media type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Media\Subtype $subtype <p>
     * MIME media subtype.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Type $type,
        private Subtype $subtype
    ) {}

    /**
     * ### Parses a serialized media type
     *
     * Converts a MIME type/subtype string into structured Type and Subtype value objects.
     *
     * <code>
     * $media = MediaType::parse('application/vnd.firehub+json');
     *
     * $media->type()->value();    // application
     * $media->subtype()->value(); // vnd.firehub+json
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the media type into type and subtype.
     * @uses \FireHub\Core\Arr\Inspection::count() To validate the number of parts.
     *
     * @param non-empty-string $value <p>
     * Serialized MIME media type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media type or subtype is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     *
     * @return self Parsed media type.
     */
    public static function parse (string $value):self {

        /** @var array{0: non-empty-string, 1: non-empty-string} $parts */
        $parts = Runtime\Str\SB\Delimiter::explode($value, '/', 2);

        if (Runtime\Arr\Inspection::count($parts) !== 2)
            throw new InvalidMediaTypeException;

        return new self(
            new Type($parts[0]),
            new Subtype($parts[1])
        );

    }

    /**
     * ### Returns the primary media type
     *
     * Returns the primary MIME type component.
     *
     * <code>
     * $media = MediaType::parse('application/json');
     *
     * $media->type()->value(); // application
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Media\Type Primary media type.
     */
    public function type ():Type {

        return $this->type;

    }

    /**
     * ### Returns the media subtype
     *
     * Returns the MIME subtype component.
     *
     * <code>
     * $media = MediaType::parse('application/json');
     *
     * $media->subtype()->value(); // json
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Media\Subtype Media subtype.
     */
    public function subtype ():Subtype {

        return $this->subtype;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $media = new MediaType(
     *     new Type('application'),
     *     new Subtype('json')
     * );
     *
     * $media->value(); // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Type::value() To get the primary type.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Subtype::value() To get the subtype.
     *
     * @return non-empty-string Serialized media type.
     */
    public function value ():string {

        return $this->type->value() . '/' . $this->subtype->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the media type.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->value());

    }

}