<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Router\Matcher;

use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Adapter\HTTP\Request\Method\Standard;

/**
 * ### Resolves allowed HTTP methods
 *
 * Collects unique HTTP methods and includes methods supported implicitly by the router.
 *
 * GET implicitly enables HEAD.
 *
 * Registered route paths implicitly support OPTIONS.
 * @since 1.0.0
 */
final class AllowedMethods {

    /**
     * ### Resolves allowed HTTP methods
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Matcher\AllowedMethods;
     * use FireHub\Adapter\HTTP\Request\Method\Standard;
     *
     * $methods = AllowedMethods::resolve(
     *     Standard::GET,
     *     Standard::POST
     * );
     *
     * // GET, POST, HEAD, OPTIONS
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To get the method value.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $first <p>
     * First allowed method.
     * </p>
     * @param \FireHub\Adapter\HTTP\Request\Method ...$others <p>
     * Additional methods.
     * </p>
     *
     * @return non-empty-list<\FireHub\Adapter\HTTP\Request\Method> Resolved allowed methods.
     */
    public static function resolve (Method $first, Method ...$others):array {

        /** @var non-empty-list<\FireHub\Adapter\HTTP\Request\Method> $methods */
        $methods = [$first];

        /** @var array<string, true> $seen */
        $seen = [
            $first->value() => true
        ];

        foreach ($others as $method) {

            $value = $method->value();

            if (isset($seen[$value])) continue;

            $methods[] = $method;
            $seen[$value] = true;

        }

        if (
            isset($seen[Standard::GET->value])
            && !isset($seen[Standard::HEAD->value])
        ) $methods[] = Standard::HEAD;

        if (!isset($seen[Standard::OPTIONS->value]))
            $methods[] = Standard::OPTIONS;

        return $methods;

    }

}