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
 * ### Represents an immutable collection of HTTP header items
 *
 * Stores comma-separated HTTP items while preserving their original order.
 *
 * Duplicate items are allowed.
 *
 * <code>
 * $items = Items::parse(
 *     'text/html; q=0.8, application/json; q=0.9'
 * );
 *
 * $items->count(); // 2
 * </code>
 *
 * @since 1.0.0
 *
 * @extends ValueObject<string>
 */
readonly class Items extends ValueObject {

    /**
     * ### HTTP items
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Item>
     */
    final protected array $items;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses static::validate() To validate the items.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove the keys from the items array.
     *
     * @param \FireHub\Adapter\HTTP\Header\Item ...$items <p>
     * HTTP items to store.
     * </p>
     *
     * @return void
     */
    final public function __construct (Item ...$items) {

        foreach ($items as $item)
            static::validate($item);

        $this->items = Runtime\Arr\Access::values($items);

    }

    /**
     * ### Parses comma-separated HTTP items
     *
     * Commas inside quoted strings are preserved.
     *
     * <code>
     * $items = Items::parse(
     *     'text/html; q=0.8, application/json; q=0.9'
     * );
     *
     * $items->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To split the serialized HTTP items.
     * @uses \FireHub\Adapter\HTTP\Header\Item::parse() To parse the item.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the item content.
     *
     * @param string $value <p>
     * Serialized HTTP items.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the primary value is empty or an
     * attribute is invalid or duplicated.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a quoted string is unterminated.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Parsed items.
     */
    final public static function parse (string $value):static {

        if ($value === '') return new static;

        $items = [];

        foreach (Splitter::split($value, ',') as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '')
                throw new InvalidParameterException(
                    'An HTTP item cannot be empty.'
                );

            $items[] = Item::parse($segment);

        }

        return new static(...$items);

    }

    /**
     * ### Checks whether an HTTP item exists
     *
     * Returns true if at least one HTTP item has the specified primary value.
     *
     * <code>
     * $items = Items::parse(
     *     'text/html; q=0.8, application/json'
     * );
     *
     * $items->has(new Value('text/html')); // true
     * $items->has(new Value('image/png')); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::content() To retrieve the primary item value.
     * @uses \FireHub\Adapter\HTTP\Header\Value::equals() To compare primary item values.
     *
     * @param \FireHub\Adapter\HTTP\Header\Value $content <p>
     * Primary item value to search for.
     * </p>
     *
     * @return bool True if at least one matching item exists.
     */
    final public function has (Value $content):bool {

        foreach ($this->items as $item)
            if ($item->content()->equals($content))
                return true;

        return false;

    }

    /**
     * ### Gets HTTP items by their primary value
     *
     * Returns all HTTP items whose primary content matches the specified value.
     *
     * Matching items are returned in their original order.
     *
     * If no matching items exist, an empty list is returned.
     *
     * <code>
     * $items = Items::parse(
     *     'text/html; q=0.8, application/json, text/html; q=0.5'
     * );
     *
     * $matches = $items->get(new Value('text/html'));
     *
     * count($matches); // 2
     *
     * $matches[0]->value(); // text/html; q=0.8
     * $matches[1]->value(); // text/html; q=0.5
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::content() To retrieve the primary item value.
     * @uses \FireHub\Adapter\HTTP\Header\Value::equals() To compare primary item values.
     *
     * @param \FireHub\Adapter\HTTP\Header\Value $content <p>
     * Primary item value to search for.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Item> Matching HTTP items.
     */
    final public function get (Value $content):array {

        $items = [];

        foreach ($this->items as $item)
            if ($item->content()->equals($content))
                $items[] = $item;

        return $items;

    }

    /**
     * ### Appends an HTTP item
     *
     * <code>
     * $items = new Items(
     *     new Item(new Value('text/html'))
     * );
     *
     * $updated = $items->add(
     *     new Item(new Value('application/json'))
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Item $item <p>
     * HTTP item to append.
     * </p>
     *
     * @return static Updated collection.
     */
    final public function add (Item $item):static {

        return new static(...[
            ...$this->items,
            $item
        ]);

    }

    /**
     * ### Removes HTTP items matching a primary value
     *
     * <code>
     * $items = Items::parse('text/html, application/json');
     *
     * $updated = $items->remove(new Value('text/html'));
     *
     * $updated->value(); // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Value::equals() To check for item equality.
     *
     * @param \FireHub\Adapter\HTTP\Header\Value $content <p>
     * Primary item value to remove.
     * </p>
     *
     * @return static Updated collection.
     */
    final public function remove (Value $content):static {

        $items = [];

        foreach ($this->items as $item)
            if (!$item->content()->equals($content))
                $items[] = $item;

        return new static(...$items);

    }

    /**
     * ### Returns the number of HTTP items
     *
     * <code>
     * $items = Items::parse('text/html, application/json');
     *
     * $items->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of items.
     *
     * @return non-negative-int Number of items.
     */
    final public function count ():int {

        return Runtime\Arr\Inspection::count($this->items);

    }

    /**
     * ### Returns all HTTP items
     *
     * <code>
     * $items = Items::parse('text/html, application/json');
     *
     * $items->all();
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Item> Stored HTTP items.
     */
    final public function all ():array {

        return $this->items;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $items = Items::parse(
     *     'text/html;q=0.8,application/json;q=0.9'
     * );
     *
     * $items->value();
     * // text/html; q=0.8, application/json; q=0.9
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::value() To get the item value.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the items.
     */
    final public function value ():string {

        $values = [];

        foreach ($this->items as $item)
            $values[] = $item->value();

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Item> Components used for comparison.
     */
    protected function comparisonValue ():array {

        return $this->items;

    }

    /**
     * ### Validates an HTTP header item
     *
     * Allows specialized HTTP header collections to enforce their own validation rules.
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Item $item <p>
     * HTTP header item to validate.
     * </p>
     *
     * @return void
     */
    protected static function validate (Item $item):void {}


}