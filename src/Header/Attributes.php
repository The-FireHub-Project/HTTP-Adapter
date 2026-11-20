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

use FireHub\Adapter\HTTP\Header\Parsing\Splitter;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### Represents an immutable collection of HTTP attributes
 *
 * Stores valueless attributes and name-value parameters while preserving their insertion order and original name
 * casing.
 *
 * Duplicate attribute names are permitted.
 *
 * This class validates generic attribute syntax without enforcing header-specific attribute semantics.
 *
 * <code>
 * $attributes = new Attributes(
 *     new Name('Secure'),
 *     new Name('HttpOnly'),
 *     new Parameter(new Name('SameSite'), new Value('Lax'))
 * );
 *
 * $attributes->value(); // Secure; HttpOnly; SameSite=Lax
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Attributes extends Collection {

    /**
     * ### Parses a semicolon-separated HTTP attribute list
     *
     * Delimiters inside quoted strings are preserved. Surrounding HTTP, optional whitespace is ignored.
     *
     * Empty segments are rejected. Duplicate attribute names are preserved.
     *
     * <code>
     * $attributes = Attributes::parse(
     *     'Secure; HttpOnly; SameSite=Lax'
     * );
     *
     * $attributes->count(); // 3
     *
     * $attributes = Attributes::parse(
     *     'Example="first;second"; Secure'
     * );
     *
     * $attributes->get(new Name('Example'))?->value();
     * // Example="first;second"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To split attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse name-value parameters.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim attribute segments.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check for parameter syntax.
     *
     * @param string $value <p>
     * Serialized HTTP attributes.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If an attribute is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a quoted string is unterminated.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed attributes.
     */
    public static function parse (string $value):self {

        if ($value === '') return new self;

        $attributes = [];

        foreach (Splitter::split($value, ';') as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '')
                throw new InvalidParameterException(
                    'An HTTP attribute cannot be empty.'
                );

            $attributes[] = Runtime\Str\SB\Search::contains('=', $segment)
                ? Parameter::parse($segment)
                : new Name($segment);

        }

        return new self(...$attributes);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $attributes = new Attributes(
     *     new Parameter(new Name('SameSite'), new Value('Lax'))
     * );
     *
     * $attribute = $attributes->get(new Name('SameSite'));
     *
     * $attribute?->value(); // SameSite=Lax
     *
     * $attributes->get(new Name('samesite')); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To get the attribute name.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare attribute names.
     */
    public function get (Name $name):null|Name|Parameter {

        foreach ($this->parameters as $attribute) {

            $current = $attribute instanceof Parameter
                ? $attribute->name()
                : $attribute;

            if ($current->equals($name))
                return $attribute;

        }

        return null;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $attributes = new Attributes(
     *     new Name('Secure'),
     *     new Name('HttpOnly'),
     *     new Parameter(new Name('SameSite'), new Value('Lax'))
     * );
     *
     * $updated = $attributes->remove(new Name('Secure'));
     *
     * $updated->value(); // HttpOnly; SameSite=Lax
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To get the attribute name.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare attribute names.
     */
    public function remove (Name $name):self {

        $attributes = [];

        foreach ($this->parameters as $attribute) {

            $current = $attribute instanceof Parameter
                ? $attribute->name()
                : $attribute;

            if (!$current->equals($name))
                $attributes[] = $attribute;

        }

        return new self(...$attributes);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $attributes = new Attributes(
     *     new Name('Secure'),
     *     new Name('HttpOnly'),
     *     new Parameter(new Name('SameSite'), new Value('Lax'))
     * );
     *
     * $attributes->value(); // Secure; HttpOnly; SameSite=Lax
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::value() To get the attribute value.
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() To get the attribute value.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join attribute values with semicolons.
     */
    public function value ():string {

        $values = [];

        foreach ($this->parameters as $attribute)
            $values[] = $attribute->value();

        return Runtime\Str\SB\Delimiter::implode($values, '; ');

    }

}