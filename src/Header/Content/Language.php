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
    Language\Tag,
    Item, Items
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;

/**
 * ### HTTP Content-Language Header
 *
 * Represents the HTTP Content-Language header.
 *
 * A Content-Language header identifies the natural languages of the intended audience for an HTTP representation.
 *
 * Each entry is represented by an HTTP Item containing a language tag without attributes.
 *
 * All items are automatically normalized during construction:
 * - Generic language values are converted into Language\Tag instances.
 * - Language tags are validated.
 * - Original language-tag casing is preserved.
 * - Wildcards and attributes are rejected.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\{
 *     Content\Language,
 *     Language\Tag,
 *     Item, Value
 * };
 *
 * // Parse a Content-Language header.
 * $language = Language::parse('hr-HR, en-US');
 *
 * $language->count(); // 2
 * $language->value(); // hr-HR, en-US
 *
 * // Parsed language tags are strongly typed.
 * $items = $language->all();
 *
 * $items[0]->content() instanceof Tag; // true
 *
 * // Create a Content-Language header.
 * $language = new Language(
 *     new Item(new Value('hr-HR')),
 *     new Item(new Tag('en-US'))
 * );
 *
 * $language->value(); // hr-HR, en-US
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
     * @uses \FireHub\Adapter\HTTP\Header\Item::content() To retrieve the language tag.
     * @uses \FireHub\Adapter\HTTP\Header\Item::attributes() To retrieve item attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::count() To check for unsupported attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the serialized language tag.
     * @uses \FireHub\Adapter\HTTP\Header\Language\Tag::parse() To parse the language tag.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language tag or attributes
     * are invalid.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If an invalid guard exception is returned.
     *
     * @return \FireHub\Adapter\HTTP\Header\Item Normalized language item.
     */
    protected static function normalize (Item $item):Item {

        if ($item->attributes()->count() !== 0)
            throw new InvalidFieldValueException(
                'Content-Language does not support attributes.'
            );

        $content = $item->content();

        if (!$content instanceof Tag) {

            $value = $content->value();

            if ($value === '')
                throw new InvalidFieldValueException(
                    'Content-Language language tag cannot be empty.'
                );

            $content = Tag::parse($value);

        }

        return new Item($content);

    }

}