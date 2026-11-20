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
use FireHub\Runtime;

/**
 * ### HTTP Header Parameter Collection
 *
 * Provides shared immutable collection behavior for HTTP header names and name-value parameters.
 *
 * The collection preserves insertion order and duplicate names.
 *
 * Concrete implementations define their own parsing and serialization rules.
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 *
 * @phpstan-consistent-constructor
 */
abstract readonly class Collection extends ValueObject {

    /**
     * ### Collection Elements
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter>
     */
    protected array $parameters;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To normalize array indexes.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter ...$parameters <p>
     * Collection elements.
     * </p>
     *
     * @return void
     */
    public function __construct (Name|Parameter ...$parameters) {

        $this->parameters = Runtime\Arr\Access::values($parameters);

    }

    /**
     * ### Checks Whether an Element Exists
     *
     * Searches the collection by name.
     *
     * <code>
     * $collection->has(new Name('charset'));
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Element name.
     * </p>
     *
     * @return bool True if the element exists, otherwise false.
     */
    public function has (Name $name):bool {

        return $this->get($name) !== null;

    }

    /**
     * ### Gets an Element by Name
     *
     * Returns the first matching element.
     *
     * Names are compared case-insensitively.
     *
     * <code>
     * $collection->get(new Name('CHARSET'));
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare element names.
     *
     * @param Name $name <p>
     * Element name.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter Matching element or null.
     */
    public function get (Name $name):Name|Parameter|null {

        foreach ($this->parameters as $parameter) {

            $current = $parameter instanceof Parameter
                ? $parameter->name()
                : $parameter;

            if ($current->equalsIgnoreCase($name))
                return $parameter;

        }

        return null;

    }

    /**
     * ### Adds an Element
     *
     * Appends an element while preserving existing elements, insertion order, and duplicate names.
     *
     * <code>
     * $updated = $collection->add(
     *     new Parameter(new Name('charset'), new Value('utf-8'))
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter $parameter <p>
     * Element to append.
     * </p>
     *
     * @return static Updated collection.
     */
    public function add (Name|Parameter $parameter):static {

        return new static(...[
            ...$this->parameters,
            $parameter
        ]);

    }

    /**
     * ### Removes Elements by Name
     *
     * Removes all elements matching the specified name.
     *
     * Names are compared case-insensitively.
     *
     * <code>
     * $updated = $collection->remove(new Name('charset'));
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare element names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Element name to remove.
     * </p>
     *
     * @return static Updated collection.
     */
    public function remove (Name $name):static {

        $parameters = [];

        foreach ($this->parameters as $parameter) {

            $current = $parameter instanceof Parameter
                ? $parameter->name()
                : $parameter;

            if (!$current->equalsIgnoreCase($name))
                $parameters[] = $parameter;

        }

        return new static(...$parameters);

    }

    /**
     * ### Counts Elements
     *
     * <code>
     * $collection->count(); // 3
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count collection elements.
     *
     * @return non-negative-int Number of elements.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->parameters);

    }

    /**
     * ### Gets All Elements
     *
     * Returns elements in insertion order.
     *
     * <code>
     * $collection->all();
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter> Collection elements.
     */
    public function all ():array {

        return $this->parameters;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter> Elements to compare.
     */
    protected function comparisonValue ():array {

        return $this->parameters;

    }

}