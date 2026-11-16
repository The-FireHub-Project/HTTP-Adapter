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

namespace FireHub\Adapter\HTTP\Header\Disposition;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Disposition Type
 *
 * Represents a content disposition type.
 *
 * Supports standard and custom disposition types.
 *
 * Disposition types are case-insensitive. Their original casing is preserved for serialization, while comparisons
 * use normalized lowercase values.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Disposition\ {
 *     Type, KnownType
 * };
 *
 * $type = Type::known(KnownType::ATTACHMENT);
 *
 * $type->value(); // attachment
 *
 * $type = new Type('custom-disposition');
 *
 * $type->value(); // custom-disposition
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Type extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the disposition type.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP token syntax.
     *
     * @param non-empty-string $type <p>
     * Content disposition type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the disposition type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $type
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
                $type
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP content disposition type.'
            )
        );

    }

    /**
     * ### Creates a Known Disposition Type
     *
     * Creates a disposition type from a predefined value.
     *
     * <code>
     * $type = Type::known(KnownType::INLINE);
     *
     * $type->value(); // inline
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Disposition\KnownType $type <p>
     * Known disposition type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the disposition type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Disposition type value object.
     */
    public static function known (KnownType $type):self {

        return new self($type->value);

    }

    /**
     * ### Parses a Disposition Type
     *
     * Parses a serialized disposition type.
     *
     * <code>
     * $type = Type::parse('ATTACHMENT');
     *
     * $type->value(); // ATTACHMENT
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized disposition type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the disposition type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed disposition type.
     */
    public static function parse (string $value):self {

        return new self($value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $type = Type::known(KnownType::ATTACHMENT);
     *
     * $type->value(); // attachment
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->type;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the disposition type.
     *
     * @return lowercase-string Normalized disposition type.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->type);

    }

}