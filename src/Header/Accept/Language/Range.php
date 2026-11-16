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

namespace FireHub\Adapter\HTTP\Header\Accept\Language;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Language Range
 *
 * Represents an immutable basic language range as defined by RFC 4647.
 *
 * Supports language subtag separated by hyphens and the wildcard.
 *
 * Language ranges are case-insensitive. The original casing is preserved
 * for serialization.
 *
 * <code>
 * $range = Range::of('hr-HR');
 *
 * $range->value(); // hr-HR
 *
 * Range::any()->value(); // *
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Range extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To validate the language range.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the RFC 4647 syntax.
     *
     * @param non-empty-string $range <p>
     * Basic language range or wildcard.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $range
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A(?:\*|[A-Za-z]{1,8}(?:-[A-Za-z0-9]{1,8})*)\z/',
                $range
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP language range.'
            )
        );

    }

    /**
     * ### Creates a Language Range
     *
     * <code>
     * Range::of('hr-HR')->value(); // hr-HR
     * Range::of('en-US')->value(); // en-US
     * Range::of('zh-Hant-TW')->value(); // zh-Hant-TW
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $range <p>
     * Basic language range.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self New language range.
     */
    public static function of (string $range):self {

        return new self($range);

    }

    /**
     * ### Creates a Wildcard Language Range
     *
     * Represents any language.
     *
     * <code>
     * Range::any()->value(); // *
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
     * ### Parses a Language Range
     *
     * Removes surrounding HTTP optional whitespace before validating the language range.
     *
     * <code>
     * Range::parse(' hr-HR ')->value(); // hr-HR
     * Range::parse('*')->value();       // *
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     *
     * @param string $value <p>
     * Serialized language range.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed language range.
     */
    public static function parse (string $value):self {

        $range = Runtime\Str\SB\Transform::trim(
            $value,
            characters: " \t"
        );

        if ($range === '')
            throw new InvalidFieldValueException(
                'HTTP language range cannot be empty.'
            );

        return new self($range);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * Range::of('hr-HR')->value(); // hr-HR
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized language range.
     */
    public function value ():string {

        return $this->range;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the language range.
     *
     * @return lowercase-string Case-insensitive comparison value.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->range);

    }

}