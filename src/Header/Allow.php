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
use FireHub\Adapter\HTTP\Request\ {
    Method\Custom, Method\Standard,
    Method
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### Represents the HTTP Allow header
 *
 * Defines the set of HTTP request methods supported by a resource.
 *
 * The Allow header identifies the methods currently permitted for the target resource and is required in 405 Method
 * Not Allowed responses.
 *
 * Method names are serialized as a comma-separated list while preserving their original order.
 *
 * Duplicate methods are removed during construction.
 *
 * An empty method collection is permitted and represents a resource that currently allows no request methods.
 *
 * The Allow header does not determine which methods are supported by a route or automatically add implicit methods.
 * These responsibilities belong to the HTTP router and its method-matching components.
 *
 * <code>
 * $allow = new Allow(
 *     Standard::GET,
 *     Standard::HEAD,
 *     Standard::POST,
 *     Standard::OPTIONS
 * );
 *
 * $allow->value();
 *
 * // GET, HEAD, POST, OPTIONS
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Allow extends ValueObject {

    /**
     * ### Allowed HTTP methods
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Request\Method>
     */
    private array $methods;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method ...$methods <p>
     * Allowed HTTP request methods.
     * </p>
     *
     * @return void
     */
    public function __construct (Method ...$methods) {

        $unique = [];
        $seen = [];

        foreach ($methods as $method) {

            $name = $method->value();

            if (isset($seen[$name]))
                continue;

            $seen[$name] = true;
            $unique[] = $method;

        }

        $this->methods = $unique;

    }

    /**
     * ### Parses an HTTP Allow header
     *
     * Converts a comma-separated list of HTTP methods into an Allow header.
     *
     * Standard methods are represented by their corresponding enum cases, while extension methods are represented by
     * custom method instances.
     *
     * <code>
     * $allow = Allow::parse('GET, HEAD, POST');
     *
     * $allow->value();
     *
     * // GET, HEAD, POST
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the methods.
     *
     * @param string $value <p>
     * Serialized HTTP Allow header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If the method token is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Allow header.
     */
    public static function parse (string $value):self {

        if (Runtime\Str\SB\Transform::trim($value) === '')
            return new self();

        $methods = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ',') as $name) {

            $name = Runtime\Str\SB\Transform::trim($name);

            if ($name === '')
                throw new InvalidFieldValueException(
                    'The Allow header contains an empty HTTP method.'
                );

            $standard = Standard::tryFrom($name);

            $methods[] = $standard ?? new Custom($name);

        }

        return new self(...$methods);

    }

    /**
     * ### Gets allowed HTTP methods
     *
     * <code>
     * $allow = new Allow(
     *     Standard::GET,
     *     Standard::POST
     * );
     *
     * $allow->methods();
     *
     * // [Standard::GET, Standard::POST]
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Request\Method> Allowed HTTP methods.
     */
    public function methods ():array {

        return $this->methods;

    }

    /**
     * ### Checks whether an HTTP method is allowed
     *
     * <code>
     * $allow = new Allow(
     *     Standard::GET,
     *     Standard::POST
     * );
     *
     * $allow->contains(Standard::GET);
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP method to check.
     * </p>
     *
     * @return bool True if the method is allowed.
     */
    public function contains (Method $method):bool {

        foreach ($this->methods as $allowed)
            if ($allowed->value() === $method->value())
                return true;

        return false;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $allow = new Allow(
     *     Standard::GET,
     *     Standard::HEAD,
     *     Standard::POST
     * );
     *
     * $allow->value();
     *
     * // GET, HEAD, POST
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the methods.
     *
     * @return string Serialized Allow header value.
     */
    public function value ():string {

        $methods = [];

        foreach ($this->methods as $method)
            $methods[] = $method->value();

        return Runtime\Str\SB\Delimiter::implode($methods, ', ');

    }

}