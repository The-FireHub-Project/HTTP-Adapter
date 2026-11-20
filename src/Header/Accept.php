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
    Media\Range, Parameter\Quality, Parameter\Quoted,
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
 * All items are automatically normalized during construction:
 * - Generic media values are converted into Range instances.
 * - Generic quality parameters are converted into Quality instances.
 * - Existing Range and Quality instances are preserved.
 * - Parameter names are compared case-insensitively.
 * - Duplicate parameter names are rejected.
 * - The optional quality parameter must be the last attribute.
 *
 * Invalid media ranges, parameters, and quality weights are rejected during normalization.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\ {
 *     Media\Range, Parameter\Quality
 *     Accept, Item, Attributes, Name, Value, Parameter
 * };
 *
 * // Parse and normalize an Accept header.
 * $accept = Accept::parse(
 *     'text/html;level=1;q=0.8, application/json;q=0.9'
 * );
 *
 * $accept->count(); // 2
 *
 * // Create an Accept header using generic HTTP components.
 * $accept = new Accept(
 *     new Item(
 *         new Value('text/html'),
 *         new Attributes(
 *             new Parameter(new Name('level'), new Value('1')),
 *             new Parameter(new Name('q'), new Value('0.8'))
 *         )
 *     )
 * );
 *
 * // The stored components are automatically normalized.
 * $item = $accept->all()[0];
 *
 * $item->content() instanceof Range; // true
 * $item->attributes()->get(new Name('q')) instanceof Quality; // true
 *
 * $accept->value(); // text/html; level=1; q=0.8
 *
 * // Strongly typed components are also supported.
 * $accept = new Accept(
 *     new Item(
 *         Range::parse('application/json'),
 *         new Attributes(new Quality('0.9'))
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
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare attribute names.
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::all() To get all attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To get the attribute name.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Range::parse() To validate the media range.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of attributes.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the media range is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the quality value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     */
    protected static function normalize (Item $item):Item {

        $content = $item->content();

        if (!$content instanceof Range) {

            /** @var non-empty-string $value */
            $value = $content->value();

            $content = Range::parse($value);

        }

        $attributes = $item->attributes()->all();
        $normalized = [];
        $names = [];

        $last = Runtime\Arr\Inspection::count($attributes) - 1;
        $quality = new Name('q');

        foreach ($attributes as $index => $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidFieldValueException(
                    'Accept attributes must be HTTP parameters.'
                );

            $name = $attribute->name();

            foreach ($names as $existing)
                if ($existing->equalsIgnoreCase($name))
                    throw new InvalidFieldValueException(
                        "Duplicate Accept parameter: {$name->value()}."
                    );

            $names[] = $name;

            if (!$name->equalsIgnoreCase($quality)) {

                $normalized[] = $attribute instanceof Quoted
                    ? $attribute
                    : new Quoted(
                        $name,
                        $attribute->content()
                    );

                continue;

            }

            if ($index !== $last)
                throw new InvalidFieldValueException(
                    'The Accept quality parameter must be the last attribute.'
                );

            $normalized[] = $attribute instanceof Quality
                ? $attribute
                : new Quality($attribute->content()->value());

        }

        return new Item(
            $content,
            new Attributes(...$normalized)
        );

    }

}