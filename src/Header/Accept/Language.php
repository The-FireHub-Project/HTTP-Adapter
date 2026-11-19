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
    Language\Range as LanguageRange,
    Parameter\Quality,
    Attributes, Item, Items, Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;

/**
 * ### HTTP Accept-Language Header
 *
 * Represents the HTTP Accept-Language request header.
 *
 * An Accept-Language header contains an ordered collection of language ranges that the client prefers for an HTTP
 * response.
 *
 * Each entry is represented by an HTTP Item containing a language range and an optional quality parameter.
 *
 * Language ranges follow the basic language range syntax defined by RFC 4647. The wildcard (*) is also supported.
 *
 * All items are automatically normalized during construction:
 * - Generic language-range values are converted into Language\Range instances.
 * - Language ranges are validated according to RFC 4647.
 * - Original language-range casing is preserved.
 * - The wildcard (*) is represented by a Language\Range instance.
 * - Generic quality parameters are converted into Quality instances.
 * - Already normalized language ranges and quality parameters are preserved.
 * - Only the optional quality parameter is permitted.
 *
 * Invalid language ranges, attributes, and quality weights are rejected during normalization.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\ {
 *     Accept\Language,
 *     Language\Range as LanguageRange,
 *     Parameter\Quality,
 *     Attributes, Item, Name, Parameter, Value
 * };
 *
 * // Parse and normalize an Accept-Language header.
 * $language = Language::parse(
 *     'hr-HR, en-US;q=0.9, en;q=0.8, *;q=0.5'
 * );
 *
 * $language->count(); // 4
 *
 * $language->value(); // hr-HR, en-US; q=0.9, en; q=0.8, *; q=0.5
 *
 * // Parsed language ranges are strongly typed.
 * $items = $language->all();
 *
 * $items[0]->content() instanceof LanguageRange; // true
 * $items[1]->content() instanceof LanguageRange; // true
 *
 * // Original language-range casing is preserved.
 * $items[0]->content()->value(); // hr-HR
 * $items[1]->content()->value(); // en-US
 *
 * // Generic quality parameters are normalized.
 * $items[1]->attributes()->get(new Name('q')) instanceof Quality; // true
 *
 * // Create an Accept-Language header using generic HTTP components.
 * $language = new Language(
 *     new Item(
 *         new Value('hr-HR')
 *     ),
 *     new Item(
 *         new Value('en-US'),
 *         new Attributes(
 *             new Parameter(new Name('q'), new Value('0.9'))
 *         )
 *     )
 * );
 *
 * $language->value(); // hr-HR, en-US; q=0.9
 *
 * // Both language ranges and quality parameters are normalized.
 * $item = $language->all()[1];
 *
 * $item->content() instanceof LanguageRange; // true
 * $item->content()->value(); // en-US
 * $item->attributes()->get(new Name('q')) instanceof Quality; // true
 *
 * // Strongly typed components are also supported.
 * $language = new Language(
 *     new Item(
 *         new LanguageRange('en-US'),
 *         new Attributes(
 *             new Quality('0.8')
 *         )
 *     )
 * );
 *
 * $language->value(); // en-US; q=0.8
 *
 * // Wildcard language range.
 * $language = Language::parse('*;q=0.5');
 *
 * $language->all()[0]->content() instanceof LanguageRange; // true
 * $language->all()[0]->content()->value(); // *
 * $language->value(); // *; q=0.5
 *
 * // A wildcard can also be created explicitly.
 * $language = new Language(
 *     new Item(
 *         LanguageRange::any()
 *     )
 * );
 *
 * $language->value(); // *
 *
 * // An empty Accept-Language header is also supported.
 * $language = Language::parse('');
 *
 * $language->count(); // 0
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Language extends Items {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::content() To retrieve the language range.
     * @uses \FireHub\Adapter\HTTP\Header\Item::attributes() To retrieve the item attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the serialized language range.
     * @uses \FireHub\Adapter\HTTP\Header\Language\Range::parse() To parse the language range.
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::all() To retrieve all attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare attribute names.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To retrieve the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::content() To retrieve the parameter value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language range or attributes
     * are invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the quality value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     */
    protected static function normalize (Item $item):Item {

        $content = $item->content();

        if (!$content instanceof LanguageRange) {

            $value = $content->value();

            if ($value === '')
                throw new InvalidFieldValueException(
                    'Accept-Language range cannot be empty.'
                );

            $content = LanguageRange::parse($value);

        }

        $attributes = [];
        $quality = new Name('q');

        foreach ($item->attributes()->all() as $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidFieldValueException(
                    'Accept-Language attributes must be HTTP parameters.'
                );

            if (!$attribute->name()->equalsIgnoreCase($quality))
                throw new InvalidFieldValueException(
                    'Accept-Language only supports the quality parameter.'
                );

            if ($attributes !== [])
                throw new InvalidFieldValueException(
                    'Duplicate Accept-Language quality parameter.'
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