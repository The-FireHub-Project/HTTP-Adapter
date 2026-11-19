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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Adapter\HTTP\Header\ {
    Media\Range, Parameter\Quality
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Accept Header
 *
 * Represents the HTTP Accept request header.
 *
 * An Accept header contains an ordered collection of media ranges with optional media parameters and quality weights.
 *
 * Each entry is represented by an HTTP Item containing a media range and its associated attributes.
 *
 * The optional quality parameter must be the last attribute and may be represented by either a generic Parameter
 * or a specialized Quality instance.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\ {
 *     Accept, Item, Attributes, Name, Value, Parameter,
 *     Media\Range, Parameter\Quality
 * };
 *
 * // Parse an Accept header.
 * $accept = Accept::parse(
 *     'text/html;level=1;q=0.8, application/json;q=0.9'
 * );
 *
 * $accept->count(); // 2
 *
 * // Create an Accept header using strongly typed quality parameters.
 * $accept = new Accept(
 *     new Item(
 *         Range::parse('text/html'),
 *         new Attributes(
 *             new Parameter(new Name('level'), new Value('1')),
 *             new Quality('0.8')
 *         )
 *     ),
 *     new Item(
 *         Range::parse('application/json'),
 *         new Attributes(new Quality('0.9'))
 *     )
 * );
 *
 * $accept->value();
 * // text/html; level=1; q=0.8, application/json; q=0.9
 *
 * // Generic quality parameters are also supported.
 * $accept = new Accept(
 *     new Item(
 *         Range::parse('application/json'),
 *         new Attributes(
 *             new Parameter(new Name('q'), new Value('0.9'))
 *         )
 *     )
 * );
 *
 * $accept->value(); // application/json; q=0.9
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Accept extends Items {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::content() To get the media range.
     * @uses \FireHub\Adapter\HTTP\Header\Item::attributes() To get the media parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To get the serialized header value.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare attribute names.
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::all() To get all attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To get the attribute name.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Range::parse() To validate the media range.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::parse() To validate the quality parameter.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of attributes.
     *
     * @param \FireHub\Adapter\HTTP\Header\Item $item <p>
     * Accept item to validate.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the media range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     */
    protected static function validate (Item $item):void {

        $content = $item->content();

        if (!$content instanceof Range) {

            /** @var non-empty-string $value */
            $value = $content->value();

            Range::parse($value);

        }

        $attributes = $item->attributes()->all();
        $last = Runtime\Arr\Inspection::count($attributes) - 1;
        $quality = new Name('q');

        foreach ($attributes as $index => $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidFieldValueException(
                    'Accept attributes must be HTTP parameters.'
                );

            if (!$attribute->name()->equals($quality))
                continue;

            if ($index !== $last)
                throw new InvalidFieldValueException(
                    'The Accept quality parameter must be the last attribute.'
                );

            if (!$attribute instanceof Quality)
                new Quality($attribute->content()->value());

        }

    }

}