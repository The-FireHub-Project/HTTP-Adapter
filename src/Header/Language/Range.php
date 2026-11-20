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
 * ### HTTP Language Range
 *
 * Represents a basic language range used in HTTP language negotiation.
 *
 * A language range identifies one or more languages using the basic language range syntax defined by RFC 4647.
 *
 * Supported forms:
 * - en
 * - en-US
 * - zh-Hant-TW
 * - *
 *
 * A basic language range consists of one to eight ASCII letters, optionally followed by a subtag containing one to
 * eight ASCII alphanumeric characters. The wildcard (*) matches any language.
 *
 * Language ranges are case-insensitive. Original casing is preserved for serialization, while equality comparisons
 * use normalized values.
 *
 * This class represents only the language range. Quality preferences are managed separately.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Language\Range;
 *
 * $range = new Range('en-US');
 *
 * $range->value(); // en-US
 * $range->isWildcard(); // false
 *
 * $range = Range::parse('zh-Hant-TW');
 *
 * $range->value(); // zh-Hant-TW
 *
 * $range = Range::any();
 *
 * $range->value(); // *
 * $range->isWildcard(); // true
 *
 * // Language ranges are compared case-insensitively.
 * $first = new Range('en-US');
 * $second = new Range('EN-us');
 *
 * $first->equals($second); // true
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Range extends Value {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the basic language range.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the basic language range.
     *
     * @param non-empty-string $value <p>
     * Basic language range or wildcard.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $value) {

        $this->guard(
            fn() => $value === '*'
                || Runtime\Str\SB\Regex::match(
                    '/\A[A-Za-z]{1,8}(?:-[A-Za-z0-9]{1,8})*\z/',
                    $value
                ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP basic language range.'
            )
        );

        parent::__construct($value);

    }

    /**
     * ### Creates a Wildcard Language Range
     *
     * Creates a language range matching any language.
     *
     * <code>
     * $range = Range::any();
     *
     * $range->value(); // *
     * $range->isWildcard(); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return self Wildcard language range.
     */
    public static function any ():self {

        return new self('*');

    }

    /**
     * ### Parses a Serialized Language Range
     *
     * Converts a serialized HTTP basic language range into a validated value object.
     *
     * The original casing is preserved.
     *
     * <code>
     * $range = Range::parse('en-US');
     *
     * $range->value(); // en-US
     *
     * $range = Range::parse('zh-Hant-TW');
     *
     * $range->value(); // zh-Hant-TW
     *
     * $range = Range::parse('*');
     *
     * $range->isWildcard(); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP basic language range.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed language range.
     */
    public static function parse (string $value):self {

        return new self($value);

    }

    /**
     * ### Checks Whether the Language Range Is a Wildcard
     *
     * Determines whether the range matches any language.
     *
     * <code>
     * $range = Range::parse('*');
     *
     * $range->isWildcard(); // true
     *
     * $range = Range::parse('en-US');
     *
     * $range->isWildcard(); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::value() To retrieve the serialized language range.
     *
     * @return bool True if the language range is a wildcard.
     */
    public function isWildcard ():bool {

        return $this->value() === '*';

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses self::value() To retrieve the serialized language range.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the language range for comparison.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->value());

    }

}