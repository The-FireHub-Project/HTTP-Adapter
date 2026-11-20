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

namespace FireHub\Adapter\HTTP\Header\Language;

use FireHub\Adapter\HTTP\Header\Value;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Language Tag
 *
 * Represents a language tag identifying a specific natural language.
 *
 * Language tags are case-insensitive and preserve their original casing for serialization.
 *
 * Unlike language ranges, language tags do not support wildcards.
 *
 * This implementation validates a commonly used subset of BCP 47 syntax.
 *
 * <code>
 * $tag = new Tag('en-US');
 *
 * $tag->value(); // en-US
 *
 * $tag = Tag::parse('zh-Hant-TW');
 *
 * $tag->value(); // zh-Hant-TW
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Tag extends Value {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the language tag.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the language tag.
     *
     * @param non-empty-string $value <p>
     * Language tag.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $value) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A[A-Za-z]{2,8}(?:-[A-Za-z0-9]{1,8})*\z/',
                $value
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP language tag.'
            )
        );

        parent::__construct($value);

    }

    /**
     * ### Parses a Language Tag
     *
     * <code>
     * $tag = Tag::parse('hr-HR');
     *
     * $tag->value(); // hr-HR
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized language tag.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed language tag.
     */
    public static function parse (string $value):self {

        return new self($value);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses self::value() To retrieve the language tag.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the language tag for comparison.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->value());

    }

}