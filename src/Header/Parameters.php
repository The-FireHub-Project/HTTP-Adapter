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
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime\Type\Arr\SortType;
use FireHub\Runtime;

/**
 * ### Represents a collection of HTTP header parameters
 *
 * Immutable value object representing an ordered collection of HTTP name-value parameters.
 *
 * Each parameter is identified by its case-insensitive name. Duplicate parameter names are not permitted,
 * regardless of their original casing.
 *
 * Parameters preserve their insertion order and are serialized using semicolon-separated HTTP parameter syntax.
 *
 * This class manages parameter collections without interpreting the semantics of individual parameters or the
 * headers containing them.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\ {
 *     Name, Value, Parameter, Parameters
 * };
 *
 * $parameters = new Parameters(f
 *     new Parameter(new Name('charset'), new Value('utf-8')),
 *     new Parameter(new Name('boundary'), new Value('abc123'))
 * );
 *
 * $parameters->count(); // 2
 *
 * $parameters->has(new Name('charset')); // true
 *
 * $parameters->get(new Name('charset'))?->content()->value(); // utf-8
 *
 * $parameters->value(); // charset=utf-8; boundary=abc123
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Parameters extends ValueObject {

    /**
     * ### Collection of HTTP parameters
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Parameter>
     */
    private array $parameters;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize parameter names.
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the parameter list.
     *
     * @param \FireHub\Adapter\HTTP\Header\Parameter ...$parameters <p>
     * HTTP header parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If duplicate parameter names are
     * provided.
     *
     * @return void
     */
    public function __construct (Parameter ...$parameters) {

        $names = [];

        foreach ($parameters as $parameter) {

            $name = Runtime\Str\SB\Casing::toLower(
                $parameter->name()->value()
            );

            if (isset($names[$name]))
                throw new InvalidParameterException(
                    'Duplicate HTTP parameter name.'
                );

            $names[$name] = true;

        }

        $this->parameters = Runtime\Arr\Access::values($parameters);

    }

    /**
     * ### Checks whether a parameter exists
     *
     * Determines whether the collection contains a parameter with the specified name.
     *
     * Parameter names are compared case-insensitive.
     *
     * <code>
     * $parameters = new Parameters(
     *     new Parameter(new Name('charset'), new Value('utf-8'))
     * );
     *
     * $parameters->has(new Name('charset')); // true
     * $parameters->has(new Name('CHARSET')); // true
     * $parameters->has(new Name('boundary')); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::get() To retrieve the matching parameter.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Parameter name to check.
     * </p>
     *
     * @return bool True if the parameter exists, false otherwise.
     */
    public function has (Name $name):bool {

        return $this->get($name) !== null;

    }

    /**
     * ### Gets a parameter by name
     *
     * Returns the first parameter matching the provided name.
     *
     * Parameter names are compared case-insensitive.
     *
     * Returns null when no matching parameter exists.
     *
     * <code>
     * $parameters = new Parameters(
     *     new Parameter(new Name('charset'), new Value('utf-8'))
     * );
     *
     * $parameter = $parameters->get(new Name('CHARSET'));
     *
     * $parameter?->content()->value(); // utf-8
     *
     * $parameters->get(new Name('missing')); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::equals() To compare parameter names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Parameter name to retrieve.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Parameter Matching parameter, or null if not found.
     */
    public function get (Name $name):?Parameter {

        foreach ($this->parameters as $parameter)
            if ($parameter->name()->equals($name))
                return $parameter;

        return null;

    }

    /**
     * ### Adds a parameter to the collection
     *
     * <code>
     * $parameters = new Parameters();
     *
     * $parameters = $parameters->add(
     *     new Parameter(
     *         new Name('charset'),
     *         new Value('utf-8')
     *     )
     * );
     *
     * $parameters->value(); // charset=utf-8
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Parameter $parameter <p>
     * Parameter to add.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If duplicate parameter names are
     * provided.
     *
     * @return self New collection containing the parameter.
     */
    public function add (Parameter $parameter):self {

        return new self(
            $parameter,
            ...$this->parameters,
        );

    }

    /**
     * ### Adds or replaces a parameter in the collection
     *
     * <code>
     * $parameters = new Parameters(
     *     new Parameter(
     *         new Name('charset'),
     *         new Value('utf-8')
     *     )
     * );
     *
     * $parameters = $parameters->set(
     *     new Parameter(
     *         new Name('charset'),
     *         new Value('iso-8859-1')
     *     )
     * );
     *
     * $parameters->value(); // charset=iso-8859-1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::has() To check for existing parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To compare parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare parameter names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Parameter $parameter <p>
     * Parameter to set.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If duplicate parameter names are
     * provided.
     *
     * @return self Updated parameter collection.
     */
    public function set (Parameter $parameter):self {

        $parameters = [];

        foreach ($this->parameters as $existing)
            $parameters[] = $existing->name()->equals($parameter->name())
                ? $parameter
                : $existing;

        if (!$this->has($parameter->name()))
            $parameters[] = $parameter;

        return new self(...$parameters);

    }

    /**
     * ### Removes a parameter from the collection
     *
     * <code>
     * $parameters = new Parameters(
     *     new Parameter(
     *         new Name('charset'),
     *         new Value('utf-8')
     *     ),
     *     new Parameter(
     *         new Name('boundary'),
     *         new Value('abc123')
     *     )
     * );
     *
     * $parameters = $parameters->remove(new Name('charset'));
     *
     * $parameters->value(); // boundary=abc123
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To compare parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare parameter names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Parameter name to remove.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If duplicate parameter names are
     * provided.
     *
     * @return self New collection without the parameter.
     */
    public function remove (Name $name):self {

        $parameters = [];

        foreach ($this->parameters as $parameter)
            if (!$parameter->name()->equals($name))
                $parameters[] = $parameter;

        return new self(...$parameters);

    }

    /**
     * ### Counts the parameters
     *
     * Returns the total number of parameters in the collection.
     *
     * <code>
     * $parameters = new Parameters(
     *     new Parameter(new Name('charset'), new Value('utf-8')),
     *     new Parameter(new Name('boundary'), new Value('abc123'))
     * );
     *
     * $parameters->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count the number of parameters.
     *
     * @return non-negative-int Number of parameters.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->parameters);

    }

    /**
     * ### Returns all parameters
     *
     * Returns all parameters in their original insertion order.
     *
     * The returned array is a copy of the internal parameter list.
     *
     * <code>
     * foreach ($parameters->all() as $parameter)
     *     echo $parameter->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Parameter> Ordered list of HTTP parameters.
     */
    public function all ():array {

        return $this->parameters;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $parameters = new Parameters(
     *     new Parameter(new Name('charset'), new Value('utf-8')),
     *     new Parameter(new Name('boundary'), new Value('abc123'))
     * );
     *
     * $parameters->value(); // charset=utf-8; boundary=abc123
     *
     * new Parameters()->value(); // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::value() To serialize individual parameters.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the parameters.
     */
    public function value ():string {

        $values = [];

        foreach ($this->parameters as $parameter)
            $values[] = $parameter->value();

        return Runtime\Str\SB\Delimiter::implode($values, '; ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To normalize parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::content() To get parameter content.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize parameter names.
     * @uses \FireHub\Runtime\Arr\Ordering::sortByKeys() To sort parameters by name.
     *
     * @return array<string, string> Normalized parameter values.
     */
    protected function comparisonValue ():array {

        $values = [];

        foreach ($this->parameters as $parameter)
            $values[Runtime\Str\SB\Casing::toLower($parameter->name()->value())] = $parameter->content()->value();

        Runtime\Arr\Ordering::sortByKeys($values, type: SortType::STRING);

        return $values;

    }

}