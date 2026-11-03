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

use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Allow header
 *
 * Identifies the HTTP request methods currently supported by a target resource.
 *
 * An empty method list is permitted and indicates that the resource does not currently allow any request methods.
 *
 * Duplicate methods are removed while preserving their original order.
 *
 * HTTP method names are case-sensitive.
 * @since 1.0.0
 */
final readonly class Allow {

    /**
     * ### Allowed HTTP request methods
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Request\Method>
     */
    private array $methods;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To get the HTTP method name.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate HTTP methods.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method ...$methods <p>
     * HTTP request methods supported by the target resource.
     * </p>
     *
     * @return void
     */
    public function __construct (Method ...$methods) {

        $unique = [];

        foreach ($methods as $method) {

            $name = $method->value();

            if (!isset($unique[$name]))
                $unique[$name] = $method;

        }

        $this->methods = Runtime\Arr\Access::values($unique);

    }

    /**
     * ### Checks whether the allowed method list is empty
     * @since 1.0.0
     *
     * @return bool True if no methods are allowed, false otherwise.
     */
    public function isEmpty ():bool {

        return $this->methods === [];

    }

    /**
     * ### Checks whether an HTTP method is allowed
     *
     * HTTP method names are compared case-sensitively.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To get the HTTP method name.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method to check.
     * </p>
     *
     * @return bool True if the method is allowed, false otherwise.
     */
    public function allows (Method $method):bool {

        foreach ($this->methods as $allowed) {

            if ($allowed->value() === $method->value())
                return true;

        }

        return false;

    }

    /**
     * ### Gets the allowed HTTP request methods
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Request\Method> Allowed HTTP methods.
     */
    public function methods ():array {

        return $this->methods;

    }

    /**
     * ### Serializes the Allow header
     *
     * Returns a comma-separated list of HTTP method names.
     *
     * An empty list produces an empty string, which is a valid Allow header field value.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To get the HTTP method name.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join HTTP method names.
     *
     * @return string Serialized Allow header value.
     */
    public function value ():string {

        $values = [];

        foreach ($this->methods as $method)
            $values[] = $method->value();

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

}