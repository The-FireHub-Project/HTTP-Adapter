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
 * ### Represents an HTTP header item
 *
 * Immutable value object containing a primary value and an optional collection of semicolon-separated attributes.
 *
 * <code>
 * $item = new Item(
 *     new Value('text/html'),
 *     new Attributes(
 *         new Parameter(new Name('q'), new Value('0.8'))
 *     )
 * );
 *
 * $item->value(); // text/html; q=0.8
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
readonly class Item extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Value $content <p>
     * Primary item value.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Attributes $attributes <p>
     * Optional item attributes.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the primary value is empty.
     *
     * @return void
     */
    public function __construct (
        private Value $content,
        private Attributes $attributes = new Attributes
    ) {

        if ($content->value() === '')
            throw new InvalidParameterException(
                'An HTTP item must have a non-empty primary value.'
            );

    }

    /**
     * ### Parses an HTTP header item
     *
     * <code>
     * $item = Item::parse('text/html; q=0.8');
     *
     * $item->content()->value(); // text/html
     * $item->attributes()->count(); // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To split the serialized HTTP item.
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::parse() To parse the item attributes.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the item content.
     * @uses \FireHub\Runtime\Arr\Mutation::shift() To remove the item content from the segments array.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP item.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the primary value is empty or an
     * attribute is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a quoted string is unterminated.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP item.
     */
    public static function parse (string $value):self {

        $segments = Splitter::split($value, ';');

        $content = Runtime\Str\SB\Transform::trim(Runtime\Arr\Mutation::shift($segments) ?? '', characters: " \t");

        if ($content === '')
            throw new InvalidParameterException(
                'An HTTP item must have a non-empty primary value.'
            );

        return new self(
            new Value($content),
            $segments === []
                ? new Attributes
                : Attributes::parse(implode(';', $segments))
        );

    }

    /**
     * ### Returns the primary item value
     *
     * <code>
     * $item = Item::parse('text/html; q=0.8');
     *
     * $item->content()->value(); // text/html
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Value Primary item value.
     */
    public function content ():Value {

        return $this->content;

    }

    /**
     * ### Returns the item attributes
     *
     * <code>
     * $item = Item::parse('text/html; q=0.8');
     *
     * $item->attributes()->has(new Name('q')); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return Attributes Item attributes.
     */
    public function attributes ():Attributes {

        return $this->attributes;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $item = Item::parse('text/html;q=0.8');
     *
     * $item->value(); // text/html; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::count() To check if the item has attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::value() To serialize the item attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the item content.
     */
    public function value ():string {

        /** @var non-empty-string $content */
        $content = $this->content->value();

        if ($this->attributes->count() === 0)
            return $content;

        return $content . '; ' . $this->attributes->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{\FireHub\Adapter\HTTP\Header\Value, \FireHub\Adapter\HTTP\Header\Attributes} Components used for
     * comparison.
     */
    protected function comparisonValue ():array {

        return [
            $this->content,
            $this->attributes
        ];

    }

}