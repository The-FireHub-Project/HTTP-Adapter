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

use FireHub\Adapter\HTTP\Header\ {
    Coding\Content\Value as CodingValue, Coding\Content\Wildcard as CodingWildcard,
    Parameter\Quality,
    Attributes, Item, Items, Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;

/**
 * ### HTTP Accept-Encoding Header
 *
 * Represents the HTTP Accept-Encoding request header.
 *
 * An Accept-Encoding header contains an ordered collection of content codings that the client is willing to accept
 * in an HTTP response.
 *
 * Each entry is represented by an HTTP Item containing a content coding and an optional quality parameter.
 *
 * Supported content codings are defined by the Content enum. The wildcard (*) is also supported.
 *
 * All items are automatically normalized during construction:
 * - Generic content-coding values are converted into Coding\Content\Value instances.
 * - Supported content codings are validated against the Content enum.
 * - Content-coding names are normalized to their canonical lowercase representation.
 * - The wildcard (*) is converted into a Coding\Content\Wildcard instance.
 * - Generic quality parameters are converted into Quality instances.
 * - Already normalized content codings, wildcards, and quality parameters are preserved.
 * - Only the optional quality parameter is permitted.
 *
 * Invalid content codings, attributes, and quality weights are rejected during normalization.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\ {
 *     Accept\Encoding,
 *     Coding\Content,
 *     Coding\Content\Value as CodingValue,
 *     Coding\Content\Wildcard as CodingWildcard,
 *     Parameter\Quality,
 *     Attributes, Item, Name, Parameter, Value
 * };
 *
 * // Parse and normalize an Accept-Encoding header.
 * $encoding = Encoding::parse(
 *     'GZIP, BR;q=0.8, deflate;q=0.5'
 * );
 *
 * $encoding->count(); // 3
 *
 * $encoding->value(); // gzip, br; q=0.8, deflate; q=0.5
 *
 * // Parsed content codings are strongly typed.
 * $items = $encoding->all();
 *
 * $items[0]->content() instanceof CodingValue; // true
 * $items[1]->content() instanceof CodingValue; // true
 *
 * // The content coding is stored as an enum.
 * $items[0]->content()->content() === Content::GZIP; // true
 *
 * // Generic quality parameters are normalized.
 * $items[1]->attributes()->get(new Name('q')) instanceof Quality; // true
 *
 * // Create an Accept-Encoding header using generic HTTP components.
 * $encoding = new Encoding(
 *     new Item(
 *         new Value('GZIP')
 *     ),
 *     new Item(
 *         new Value('BR'),
 *         new Attributes(
 *             new Parameter(new Name('q'), new Value('0.8'))
 *         )
 *     )
 * );
 *
 * $encoding->value(); // gzip, br; q=0.8
 *
 * // Both content codings and quality parameters are normalized.
 * $item = $encoding->all()[1];
 *
 * $item->content() instanceof CodingValue; // true
 * $item->content()->value(); // br
 * $item->attributes()->get(new Name('q')) instanceof Quality; // true
 *
 * // Strongly typed components are also supported.
 * $encoding = new Encoding(
 *     new Item(
 *         new CodingValue(Content::GZIP),
 *         new Attributes(
 *             new Quality('0.9')
 *         )
 *     )
 * );
 *
 * $encoding->value(); // gzip; q=0.9
 *
 * // Wildcard content coding.
 * $encoding = Encoding::parse('*;q=0.5');
 *
 * $encoding->all()[0]->content() instanceof CodingWildcard; // true
 * $encoding->value(); // *; q=0.5
 *
 * // A wildcard can also be created explicitly.
 * $encoding = new Encoding(
 *     new Item(
 *         new CodingWildcard()
 *     )
 * );
 *
 * $encoding->value(); // *
 *
 * // An empty Accept-Encoding header is also valid.
 * $encoding = Encoding::parse('');
 *
 * $encoding->count(); // 0
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Encoding extends Items {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::content() To retrieve the content coding.
     * @uses \FireHub\Adapter\HTTP\Header\Item::attributes() To retrieve the item attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the serialized coding.
     * @uses \FireHub\Adapter\HTTP\Header\Coding\Content\Value::parse() To parse the content coding.
     * @uses \FireHub\Adapter\HTTP\Header\Coding\Content\Wildcard::parse() To parse the wildcard.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quality::parse() To parse the quality parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::all() To retrieve all attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To retrieve the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::content() To retrieve the parameter value.
     *
     * @throws InvalidFieldValueException If the content coding or attributes are invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the quality value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     */
    protected static function normalize (Item $item):Item {

        $content = $item->content();

        if (!$content instanceof CodingValue && !$content instanceof CodingWildcard) {

            $value = $content->value();

            if ($value === '*') {

                $content = new CodingWildcard;

            } else {

                if ($value === '')
                    throw new InvalidFieldValueException(
                        'Accept-Encoding content coding cannot be empty.'
                    );

                $content = CodingValue::parse($value);

            }

        }

        $attributes = [];
        $quality = new Name('q');

        foreach ($item->attributes()->all() as $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidFieldValueException(
                    'Accept-Encoding attributes must be HTTP parameters.'
                );

            if (!$attribute->name()->equals($quality))
                throw new InvalidFieldValueException(
                    'Accept-Encoding only supports the quality parameter.'
                );

            $attributes[] = $attribute instanceof Quality
                ? $attribute
                : new Quality($attribute->content()->value());

        }

        return new Item(
            $content,
            new Attributes(...$attributes)
        );

    }

}