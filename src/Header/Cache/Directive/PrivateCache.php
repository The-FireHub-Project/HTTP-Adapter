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
 * ### Cache-Control Private Directive
 *
 * Represents the private response cache directive.
 *
 * Without field names, the directive prevents shared caches from storing the response while allowing private caches
 * to store it, subject to other caching requirements.
 *
 * When field names are specified, shared caches must not store those header fields but may store the remaining
 * response if otherwise permitted.
 *
 * <code>
 * $directive = new PrivateCache();
 *
 * $directive->value(); // private
 *
 * $directive = new PrivateCache(
 *     new Name('Set-Cookie'),
 *     new Name('Authorization')
 * );
 *
 * $directive->value();
 * // private="Set-Cookie, Authorization"
 * </code>
 *
 * @since 1.0.0
 */
final readonly class PrivateCache extends Parameter {

    /**
     * ### Field Names
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Name> HTTP field names
     * excluded from shared cache storage.
     */
    private array $fields;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To preserve insertion order.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join field names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name ...$fields <p>
     * Optional HTTP field names that shared caches must not store.
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
            new Name('private'),
            new Value(Runtime\Str\SB\Delimiter::implode($names, ', '))
        );

    }

    /**
     * ### Parses a Private Cache Directive
     *
     * Parses a Cache-Control private directive with an optional quoted list of HTTP field names.
     *
     * The directive name is case-insensitive.
     *
     * When field names are specified, the argument must use quoted-string syntax.
     *
     * <code>
     * $directive = PrivateCache::parse('private');
     *
     * $directive->fields(); // []
     * $directive->value();  // private
     *
     * $directive = PrivateCache::parse(
     *     'PRIVATE="Set-Cookie, Authorization"'
     * );
     *
     * $directive->fields()[0]->value(); // Set-Cookie
     * $directive->fields()[1]->value(); // Authorization
     *
     * $directive->value();
     * // private="Set-Cookie, Authorization"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the directive name.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To parse the quoted parameter.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To locate the parameter separator.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the original argument.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To inspect argument length.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split field names.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     *
     * @param non-empty-string $value <p>
     * Serialized Cache-Control private directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive name,
     * quoted field-name list, or field-name syntax is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed private cache directive.
     */
    public static function parse (string $value):self {

        $name = new Name('private');

        if (!Runtime\Str\SB\Search::contains('=', $value)) {

            if (!new Name($value)->equalsIgnoreCase($name))
                throw new InvalidParameterException(
                    'Expected the Cache-Control private directive.'
                );

            return new self();

        }

        $separator = Runtime\Str\SB\Search::firstPosition('=', $value);

        if ($separator === false)
            throw new InvalidParameterException(
                'Cache-Control private requires a quoted field-name list.'
            );

        $argument = Runtime\Str\SB\Transform::trim(
            Runtime\Str\SB\Access::part($value, $separator + 1),
            characters: " \t"
        );

        $length = Runtime\Str\SB\Inspection::length($argument);

        if (
            $length < 2
            || $argument[0] !== '"'
            || $argument[-1] !== '"'
        ) throw new InvalidParameterException(
            'Cache-Control private field-name list must be quoted.'
        );

        $parameter = Parameter\Quoted::parse($value);

        if (!$parameter->name()->equalsIgnoreCase($name))
            throw new InvalidParameterException(
                'Expected the Cache-Control private directive.'
            );

        $fields = $parameter->content()->value();

        if ($fields === '')
            throw new InvalidParameterException(
                'Cache-Control private field-name list cannot be empty.'
            );

        $names = [];

        foreach (Runtime\Str\SB\Delimiter::explode($fields, ',') as $field) {

            $field = Runtime\Str\SB\Transform::trim(
                $field,
                characters: " \t"
            );

            if ($field === '')
                throw new InvalidParameterException(
                    'Cache-Control private contains an empty field name.'
                );

            $names[] = new Name($field);

        }

        return new self(...$names);

    }

    /**
     * ### Gets the Private Header Fields
     *
     * Returns the HTTP field names excluded from shared cache storage.
     *
     * An empty list represents the unqualified private directive, which prevents shared caches from storing the
     * entire response.
     *
     * <code>
     * $directive = new PrivateCache(
     *     new Name('Set-Cookie'),
     *     new Name('Authorization')
     * );
     *
     * $directive->fields()[0]->value(); // Set-Cookie
     * $directive->fields()[1]->value(); // Authorization
     *
     * (new PrivateCache())->fields(); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Name> HTTP field names excluded from shared cache storage.
     */
    public function fields ():array {

        return $this->fields;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * (new PrivateCache())->value();
     * // private
     *
     * (new PrivateCache(new Name('Set-Cookie')))->value();
     * // private="Set-Cookie"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::content() To retrieve the field-name list.
     *
     * @return non-empty-string Serialized private cache directive.
     */
    public function value ():string {

        if ($this->fields === [])
            return 'private';

        return 'private="' . $this->content()->value() . '"';

    }

}