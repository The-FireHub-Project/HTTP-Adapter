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

use FireHub\Adapter\HTTP\Header\{
    Coding\Content as ContentCoding,
    Coding\Content\Value as CodingValue,
    Attributes, Item, Items
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;

/**
 * ### HTTP Content-Encoding Header
 *
 * Represents the HTTP Content-Encoding header.
 *
 * A Content-Encoding header contains an ordered collection of content codings applied to an HTTP representation.
 *
 * Content codings are listed in the order in which they were applied. Recipients must decode them in reverse order.
 *
 * Each entry is represented by an HTTP Item containing a content coding without attributes.
 *
 * All items are automatically normalized during construction:
 * - Generic content-coding values are converted into Coding\Content\Value instances.
 * - Supported content codings are validated against the Content enum.
 * - Content-coding names are normalized to their canonical lowercase representation.
 * - Wildcards and attributes are rejected.
 * - The identity coding is rejected because it does not represent an applied transformation.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\{
 *     Coding\Content, Coding\Content\Value as CodingValue,
 *     Content\Encoding,
 *     Item, Value
 * };
 *
 * // Parse and normalize a Content-Encoding header.
 * $encoding = Encoding::parse('GZIP, BR');
 *
 * $encoding->count(); // 2
 * $encoding->value(); // gzip, br
 *
 * // Parsed content codings are strongly typed.
 * $items = $encoding->all();
 *
 * $items[0]->content() instanceof CodingValue; // true
 * $items[0]->content()->content() === Content::GZIP; // true
 *
 * // Create a Content-Encoding header using generic HTTP components.
 * $encoding = new Encoding(
 *     new Item(new Value('GZIP')),
 *     new Item(new Value('BR'))
 * );
 *
 * $encoding->value(); // gzip, br
 *
 * // Strongly typed components are also supported.
 * $encoding = new Encoding(
 *     new Item(new CodingValue(Content::GZIP)),
 *     new Item(new CodingValue(Content::BR))
 * );
 *
 * $encoding->value(); // gzip, br
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
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::count() To check for unsupported attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the serialized coding.
     * @uses \FireHub\Adapter\HTTP\Header\Coding\Content\Value::parse() To normalize the content coding.
     * @uses \FireHub\Adapter\HTTP\Header\Coding\Content\Value::content() To retrieve the content coding enum.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the content coding or attributes
     * are invalid.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     *
     * @return \FireHub\Adapter\HTTP\Header\Item Normalized content-coding item.
     */
    protected static function normalize (Item $item):Item {

        if ($item->attributes()->count() !== 0)
            throw new InvalidFieldValueException(
                'Content-Encoding does not support attributes.'
            );

        $content = $item->content();

        if (!$content instanceof CodingValue) {

            $value = $content->value();

            if ($value === '')
                throw new InvalidFieldValueException(
                    'Content-Encoding content coding cannot be empty.'
                );

            $content = CodingValue::parse($value);

        }

        if ($content->content() === ContentCoding::IDENTITY)
            throw new InvalidFieldValueException(
                'Content-Encoding must not explicitly specify identity.'
            );

        return new Item($content);

    }

}