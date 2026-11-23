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

namespace FireHub\Adapter\HTTP\Router\Matcher;

use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Adapter\HTTP\Request\Method\ {
    Custom, Standard
};

/**
 * ### Represents allowed HTTP methods
 *
 * Provides the effective HTTP request methods supported by a matched route pattern, including implicit HEAD and
 * OPTIONS support.
 *
 * Standard HTTP methods are represented by their corresponding enum cases, while extension methods are represented
 * by custom method objects.
 *
 * Duplicate methods are removed while preserving their original order.
 *
 * <code>
 * $allowed = new AllowedMethods(
 *     Standard::GET,
 *     Standard::POST
 * );
 *
 * $allowed->methods();
 *
 * // [Standard::GET, Standard::POST, Standard::HEAD, Standard::OPTIONS]
 * </code>
 *
 * @since 1.0.0
 */
final readonly class AllowedMethods {

    /**
     * ### Effective HTTP methods
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
     * Explicitly registered HTTP request methods.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If a method token is invalid.
     *
     * @return void
     */
    public function __construct (Method ...$methods) {

        $effective = []; $seen = [];
        foreach ($methods as $method) {

            $name = $method->value();

            if (isset($seen[$name]))
                continue;

            $seen[$name] = true;

            $effective[] = $method;

        }

        if (
            isset($seen[Standard::GET->value])
            && !isset($seen[Standard::HEAD->value])
        ) $effective[] = Standard::HEAD;

        if (
            $effective !== []
            && !isset($seen[Standard::OPTIONS->value])
        ) $effective[] = Standard::OPTIONS;

        $this->methods = $effective;

    }

    /**
     * ### Checks whether an HTTP method is allowed
     *
     * Determines whether the specified HTTP method belongs to the effective set of supported methods.
     *
     * Method names are compared case-sensitive.
     *
     * <code>
     * $allowed = new AllowedMethods(['GET']);
     *
     * $allowed->contains(Standard::GET);  // true
     * $allowed->contains(Standard::HEAD); // true
     * $allowed->contains(Standard::POST); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method to check.
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
     * ### Gets allowed HTTP methods
     *
     * Returns the effective HTTP methods as standard enum cases or custom method objects.
     *
     * <code>
     * $allowed = new AllowedMethods([
     *     'GET',
     *     'POST'
     * ]);
     *
     * $allowed->methods();
     *
     * // [Standard::GET, Standard::POST, Standard::HEAD, Standard::OPTIONS]
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
     * ### Gets allowed HTTP method names
     *
     * Returns the effective HTTP methods as their serialized string values.
     *
     * <code>
     * $allowed = new AllowedMethods([
     *     'GET',
     *     'POST'
     * ]);
     *
     * $allowed->values();
     *
     * // ['GET', 'POST', 'HEAD', 'OPTIONS']
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<non-empty-string> Allowed HTTP method names.
     */
    public function values ():array {

        $values = [];

        foreach ($this->methods as $method)
            $values[] = $method->value();

        return $values;

    }

}