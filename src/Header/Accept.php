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

use FireHub\Adapter\HTTP\Header\Media\Range;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Accept Header
 *
 * Represents the HTTP Accept request header.
 *
 * An Accept header contains an ordered collection of media ranges with optional media parameters, quality weights,
 * and accept extensions.
 *
 * Each entry is represented by an HTTP Item containing a media range and its associated attributes.
 *
 * <code>
 * $accept = Accept::parse(
 *     'text/html;q=0.8, application/json;q=0.9'
 * );
 *
 * $accept->count(); // 2
 *
 * $accept->value();
 * // text/html; q=0.8, application/json; q=0.9
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
     * @uses \FireHub\Adapter\HTTP\Header\Item::attributes() To get the quality weight.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To check for the quality weight name.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To get the quality weight value.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Range::parse() To validate the media range.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate quality weights.
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

            $value = $content->value();

            if ($value === '')
                throw new InvalidFieldValueException(
                    'An Accept media range cannot be empty.'
                );

            Range::parse($value);

        }

        foreach ($item->attributes()->all() as $attribute) {

            $name = $attribute instanceof Parameter
                ? $attribute->name()
                : $attribute;

            if (!$name->equals(new Name('q')))
                continue;

            if (!$attribute instanceof Parameter)
                throw new InvalidFieldValueException(
                    'The Accept quality parameter requires a value.'
                );

            $quality = $attribute->content()->value();

            if (!Runtime\Str\SB\Regex::match(
                '/\A(?:0(?:\.[0-9]{0,3})?|1(?:\.0{0,3})?)\z/',
                $quality
            )) throw new InvalidFieldValueException(
                'Invalid Accept quality value.'
            );

        }

    }

}