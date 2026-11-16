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

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Language\Tag;
use FireHub\Runtime;

/**
 * ### HTTP Content-Language Header
 *
 * Represents an immutable collection of language tags describing the intended audience of an HTTP representation.
 *
 * Language tags preserve their original order and casing.
 *
 * <code>
 * $language = Language::of(
 *     Tag::of('hr-HR'),
 *     Tag::of('en-US')
 * );
 *
 * $language->count(); // 2
 * $language->value(); // hr-HR, en-US
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Language extends ValueObject {

    /**
     * ### Language Tags
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Language\Tag>
     */
    private array $tags;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the language tags.
     *
     * @param \FireHub\Adapter\HTTP\Header\Language\Tag ...$tags <p>
     * Language tags describing the intended audience.
     * </p>
     *
     * @return void
     */
    public function __construct (Tag ...$tags) {

        $this->tags = Runtime\Arr\Access::values($tags);

    }

    /**
     * ### Creates an HTTP Content-Language Header
     *
     * Creates a Content-Language header from one or more language tags.
     *
     * <code>
     * $language = Language::of(
     *     Tag::of('hr-HR'),
     *     Tag::of('en-US')
     * );
     *
     * $language->value(); // hr-HR, en-US
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Language\Tag ...$tags <p>
     * Language tags.
     * </p>
     *
     * @return self New Content-Language header.
     */
    public static function of (Tag ...$tags):self {

        return new self(...$tags);

    }

    /**
     * ### Parses an HTTP Content-Language Header
     *
     * Parses comma-separated language tags.
     *
     * Empty list members are ignored.
     *
     * <code>
     * $language = Language::parse(
     *     'hr-HR, en-US'
     * );
     *
     * $language->count(); // 2
     * $language->value(); // hr-HR, en-US
     *
     * Language::parse('')->count(); // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the header value.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Adapter\HTTP\Header\Language\Tag::parse() To parse individual language tags.
     *
     * @param string $value <p>
     * Serialized Content-Language header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Content-Language header.
     */
    public static function parse (string $value):self {

        $tags = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ',') as $part) {

            $part = Runtime\Str\SB\Transform::trim(
                $part,
                characters: " \t"
            );

            if ($part === '') continue;

            $tags[] = Tag::parse($part);

        }

        return new self(...$tags);

    }

    /**
     * ### Counts Language Tags
     *
     * Returns the number of language tags in the collection.
     *
     * <code>
     * $language = Language::of(
     *     Tag::of('hr-HR'),
     *     Tag::of('en-US')
     * );
     *
     * $language->count(); // 2
     *
     * Language::of()->count(); // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count language tags.
     *
     * @return non-negative-int Number of language tags.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->tags);

    }

    /**
     * ### Gets All Language Tags
     *
     * Returns language tags in their original order.
     *
     * <code>
     * $language = Language::of(
     *     Tag::of('hr-HR'),
     *     Tag::of('en-US')
     * );
     *
     * $tags = $language->all();
     *
     * $tags[0]->value(); // hr-HR
     * $tags[1]->value(); // en-US
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Language\Tag> Language tags.
     */
    public function all ():array {

        return $this->tags;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $language = Language::of(
     *     Tag::of('hr-HR'),
     *     Tag::of('en-US')
     * );
     *
     * $language->value(); // hr-HR, en-US
     *
     * Language::of()->value(); // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Language\Tag::value() To serialize language tags.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join serialized tags.
     */
    public function value ():string {

        $values = [];

        foreach ($this->tags as $tag)
            $values[] = $tag->value();

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Language\Tag> Language tags.
     */
    protected function comparisonValue ():array {

        return $this->tags;

    }

}