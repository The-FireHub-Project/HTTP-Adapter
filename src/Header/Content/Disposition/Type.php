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

namespace FireHub\Adapter\HTTP\Header\Content\Disposition;

use FireHub\Adapter\HTTP\Header\Value;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Disposition Type
 *
 * Represents a validated disposition type.
 *
 * Disposition types are case-insensitive and normalized to lowercase.
 *
 * Provides named factory methods for standard disposition types and supports extension types through parsing.
 *
 * <code>
 * Type::inline()->value();     // inline
 * Type::attachment()->value(); // attachment
 * Type::formData()->value();   // form-data
 *
 * Type::parse('CUSTOM')->value(); // custom
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Type extends Value {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the disposition type.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the disposition type.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the disposition type.
     *
     * @param non-empty-string $type <p>
     * Disposition type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    private function __construct (string $type) {

        $type = Runtime\Str\SB\Casing::toLower($type);

        $this->guard(
            static fn():bool => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
                $type
            ),
            static fn() => new InvalidFieldValueException(
                'Invalid Content-Disposition type.'
            )
        );

        parent::__construct($type);

    }

    /**
     * ### Parses a Disposition Type
     *
     * Creates a disposition type from a standard or extension token.
     *
     * <code>
     * Type::parse('ATTACHMENT')->value(); // attachment
     * Type::parse('custom')->value();     // custom
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized disposition type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed disposition type.
     */
    public static function parse (string $value):self {

        return new self($value);

    }

    /**
     * ### Creates an Inline Disposition
     *
     * Indicates that the content should be displayed inline.
     *
     * <code>
     * Type::inline()->value(); // inline
     * </code>
     *
     * @since 1.0.0
     *
     * @return self Inline disposition type.
     */
    public static function inline ():self {

        return new self('inline');

    }

    /**
     * ### Creates an Attachment Disposition
     *
     * Indicates that the content should be handled as an attachment.
     *
     * <code>
     * Type::attachment()->value(); // attachment
     * </code>
     *
     * @since 1.0.0
     *
     * @return self Attachment disposition type.
     */
    public static function attachment ():self {

        return new self('attachment');

    }

    /**
     * ### Creates a Form-Data Disposition
     *
     * Indicates that the content represents a multipart form-data field.
     *
     * <code>
     * Type::formData()->value(); // form-data
     * </code>
     *
     * @since 1.0.0
     *
     * @return self Form-data disposition type.
     */
    public static function formData ():self {

        return new self('form-data');

    }

}