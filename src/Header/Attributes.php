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

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Parsing\Splitter;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### Represents an immutable collection of HTTP attributes
 *
 * Stores valueless attributes and name-value parameters while preserving their insertion order and original name
 * casing.
 *
 * Attribute names are unique and compared case-insensitively.
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
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Attributes extends ValueObject {

    /**
     * ### Attributes
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter>
     */
    private array $attributes;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To preserve insertion order.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter ...$attributes <p>
     * Attributes to store.
     * </p>
     *
     * @return void
     */
    public function __construct (Name|Parameter ...$attributes) {

        $this->attributes = Runtime\Arr\Access::values($attributes);

    }

    /**
     * ### Parses a semicolon-separated HTTP attribute list
     *
     * Delimiters inside quoted strings are preserved. Surrounding HTTP, optional whitespace is ignored.
     *
     * Empty segments and duplicate attribute names are rejected.
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
     * ### Checks whether an attribute exists
     *
     * <code>
     * $attributes = new Attributes(
     *     new Name('HttpOnly')
     * );
     *
     * $attributes->has(new Name('httponly')); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::get() To check for attribute existence.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Attribute name to search for.
     * </p>
     *
     * @return bool True if the attribute exists.
     */
    public function has (Name $name):bool {

        return $this->get($name) !== null;

    }

    /**
     * ### Gets an attribute by name
     *
     * <code>
     * $attributes = new Attributes(
     *     new Parameter(new Name('SameSite'), new Value('Lax'))
     * );
     *
     * $attribute = $attributes->get(new Name('samesite'));
     *
     * $attribute?->value(); // SameSite=Lax
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To get the attribute name.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare attribute names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Attribute name to search for.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter Matching attribute or null.
     */
    public function get (Name $name):null|Name|Parameter {

        foreach ($this->attributes as $attribute) {

            $current = $attribute instanceof Parameter
                ? $attribute->name()
                : $attribute;

            if ($current->equals($name))
                return $attribute;

        }

        return null;

    }

    /**
     * ### Adds an HTTP Attribute
     *
     * Appends an attribute to the collection while preserving existing attributes, insertion order, and duplicate
     * names.
     *
     * <code>
     * $attributes = new Attributes(
     *     new Parameter(new Name('id'), new Value('123'))
     * );
     *
     * $updated = $attributes->add(
     *     new Parameter(new Name('id'), new Value('456'))
     * );
     *
     * $updated->value(); // id=123; id=456
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter $attribute <p>
     * Attribute to append.
     * </p>
     *
     * @return self Updated collection.
     */
    public function add (Name|Parameter $attribute):self {

        return new self(...[
            ...$this->attributes,
            $attribute
        ]);

    }

    /**
     * ### Removes an HTTP attribute
     *
     * Removes the attribute matching the specified name. Attribute names are compared case-insensitive.
     *
     * If the attribute does not exist, the collection remains unchanged.
     *
     * <code>
     * $attributes = new Attributes(
     *     new Name('Secure'),
     *     new Name('HttpOnly'),
     *     new Parameter(new Name('SameSite'), new Value('Lax'))
     * );
     *
     * $updated = $attributes->remove(new Name('secure'));
     *
     * $updated->value(); // HttpOnly; SameSite=Lax
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To get the attribute name.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare attribute names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Name of the attribute to remove.
     * </p>
     *
     * @return self Updated collection without the specified attribute.
     */
    public function remove (Name $name):self {

        $attributes = [];

        foreach ($this->attributes as $attribute) {

            $current = $attribute instanceof Parameter
                ? $attribute->name()
                : $attribute;

            if (!$current->equals($name))
                $attributes[] = $attribute;

        }

        return new self(...$attributes);

    }

    /**
     * ### Returns the number of attributes
     *
     * <code>
     * $attributes = new Attributes(
     *     new Name('Secure'),
     *     new Name('HttpOnly')
     * );
     *
     * $attributes->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of attributes.
     *
     * @return non-negative-int Number of attributes.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->attributes);

    }

    /**
     * ### Returns all attributes
     *
     * <code>
     * $attributes = new Attributes(
     *     new Name('Secure'),
     *     new Name('HttpOnly')
     * );
     *
     * $attributes->all();
     * // [Name('Secure'), Name('HttpOnly')]
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter> Stored attributes.
     */
    public function all ():array {

        return $this->attributes;

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

        foreach ($this->attributes as $attribute)
            $values[] = $attribute->value();

        return Runtime\Str\SB\Delimiter::implode($values, '; ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter> Attributes to compare.
     */
    protected function comparisonValue ():array {

        return $this->attributes;

    }

}