<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Cache\Directive;

use FireHub\Adapter\HTTP\Header\{
    Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### Cache-Control No-Cache Directive
 *
 * Represents the no-cache cache directive.
 *
 * Requires successful validation before a stored response can be reused.
 *
 * In response, an optional quoted list of field names restricts the validation requirement to the specified header
 * fields.
 *
 * <code>
 * $directive = new NoCache();
 *
 * $directive->value(); // no-cache
 *
 * $directive = new NoCache(
 *     new Name('Set-Cookie'),
 *     new Name('Authorization')
 * );
 *
 * $directive->value();
 * // no-cache="Set-Cookie, Authorization"
 * </code>
 *
 * @since 1.0.0
 */
final readonly class NoCache extends Parameter {

    /**
     * ### Field Names
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Name> HTTP field names requiring validation.
     */
    private array $fields;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To preserve insertion order.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name ...$fields <p>
     * Optional HTTP field names requiring validation.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (Name ...$fields) {

        $this->fields = Runtime\Arr\Access::values($fields);

        $names = [];
        foreach ($fields as $field)
            $names[] = $field->value();

        parent::__construct(
            new Name('no-cache'),
            new Value(implode(', ', $names))
        );

    }

    /**
     * ### Parses a No-Cache Directive
     *
     * Parses a Cache-Control no-cache directive with an optional quoted list of HTTP field names.
     *
     * The directive name is case-insensitive. When field names are specified, they must be enclosed in double quotes.
     *
     * <code>
     * $directive = NoCache::parse('no-cache');
     *
     * $directive->fields(); // []
     * $directive->value();  // no-cache
     *
     * $directive = NoCache::parse(
     *     'NO-CACHE="Set-Cookie, Authorization"'
     * );
     *
     * $directive->fields()[0]->value(); // Set-Cookie
     * $directive->fields()[1]->value(); // Authorization
     *
     * $directive->value();
     * // no-cache="Set-Cookie, Authorization"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the directive name.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To parse the quoted parameter value.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To inspect the serialized directive.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace around field names.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the quoted field-name list.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To check for the quoted field-name list.
     *
     * @param non-empty-string $value <p>
     * Serialized Cache-Control no-cache directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive name, quoted
     * field-name list, or field-name syntax is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed no-cache directive.
     */
    public static function parse (string $value):self {

        $name = new Name('no-cache');

        if (new Name($value)->equalsIgnoreCase($name))
            return new self();

        $parameter = Parameter\Quoted::parse($value);

        if (!$parameter->name()->equalsIgnoreCase($name))
            throw new InvalidParameterException(
                'Expected the Cache-Control no-cache directive.'
            );

        $fields = $parameter->content()->value();

        if ($fields === '')
            throw new InvalidParameterException(
                'Cache-Control no-cache field-name list cannot be empty.'
            );

        $serialized = $parameter->value();

        $separator = Runtime\Str\SB\Search::firstPosition('=', $serialized);

        if ($separator === false)
            throw new InvalidParameterException(
                'Cache-Control no-cache requires a quoted field-name list.'
            );

        $argument = Runtime\Str\SB\Access::part($serialized, $separator + 1);

        if (
            Runtime\Str\SB\Inspection::length($argument) < 2
            || $argument[0] !== '"'
            || $argument[-1] !== '"'
        ) throw new InvalidParameterException(
            'Cache-Control no-cache field-name list must be quoted.'
        );

        $names = [];

        foreach (Runtime\Str\SB\Delimiter::explode($fields, ',') as $field) {

            $field = Runtime\Str\SB\Transform::trim($field);

            if ($field === '')
                throw new InvalidParameterException(
                    'Cache-Control no-cache contains an empty field name.'
                );

            $names[] = new Name($field);

        }

        return new self(...$names);

    }

    /**
     * ### Gets the Restricted Header Fields
     *
     * Returns the field names requiring validation.
     *
     * An empty list indicates the unqualified no-cache directive.
     *
     * <code>
     * $directive = new NoCache(
     *     new Name('Set-Cookie')
     * );
     *
     * $directive->fields()[0]->value(); // Set-Cookie
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Name> Restricted HTTP field names.
     */
    public function fields ():array {

        return $this->fields;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * (new NoCache())->value(); // no-cache
     *
     * new NoCache(new Name('Authorization'))->value();
     * // no-cache="Authorization"
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized no-cache directive.
     */
    public function value ():string {

        if ($this->fields === [])
            return 'no-cache';

        return 'no-cache="' . $this->content()->value() . '"';

    }

}