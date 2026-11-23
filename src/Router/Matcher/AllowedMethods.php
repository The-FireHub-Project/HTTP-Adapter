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
use FireHub\Adapter\HTTP\Request\Method\Standard;

/**
 * ### Represents allowed HTTP methods
 *
 * Provides the effective HTTP methods supported by a matched route pattern, including implicit HEAD and OPTIONS
 * support.
 *
 * <code>
 * $allowed = new AllowedMethods([
 *     'GET',
 *     'POST'
 * ]);
 *
 * $allowed->values();
 *
 * // ['GET', 'HEAD', 'POST', 'OPTIONS']
 * </code>
 *
 * @since 1.0.0
 */
final readonly class AllowedMethods {

    /**
     * ### Effective HTTP methods
     * @since 1.0.0
     *
     * @var list<non-empty-string>
     */
    private array $methods;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param list<non-empty-string> $methods <p>
     * Explicitly registered HTTP methods.
     * </p>
     *
     * @return void
     */
    public function __construct (array $methods) {

        $effective = [];
        $seen = [];

        foreach ($methods as $method) {

            if (isset($seen[$method]))
                continue;

            $seen[$method] = true;
            $effective[] = $method;

            if (
                $method === Standard::GET->value
                && !isset($seen[Standard::HEAD->value])
            ) {

                $seen[Standard::HEAD->value] = true;
                $effective[] = Standard::HEAD->value;

            }

        }

        if (
            $effective !== []
            && !isset($seen[Standard::OPTIONS->value])
        ) $effective[] = Standard::OPTIONS->value;

        $this->methods = $effective;

    }

    /**
     * ### Checks whether an HTTP method is allowed
     *
     * <code>
     * $allowed->contains(Standard::HEAD);
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To retrieve the method value.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     *
     * @return bool True if the method is allowed.
     */
    public function contains (Method $method):bool {

        foreach ($this->methods as $allowed)
            if ($allowed === $method->value())
                return true;

        return false;

    }

    /**
     * ### Gets allowed HTTP methods
     *
     * <code>
     * $allowed->values();
     *
     * // ['GET', 'HEAD', 'POST', 'OPTIONS']
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<non-empty-string> Allowed HTTP methods.
     */
    public function values ():array {

        return $this->methods;

    }

}