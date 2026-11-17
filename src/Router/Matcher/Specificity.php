<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Router\Matcher;

use FireHub\Adapter\HTTP\Router\Path\Pattern;
use FireHub\Runtime;

/**
 * ### Compares HTTP route path specificity
 *
 * Determines the relative specificity of two route path patterns.
 *
 * Static path segments have higher priority than dynamic parameter segments.
 *
 * Patterns are compared from left to right, and the first difference determines which pattern is more specific.
 *
 * Pattern comparison is independent of HTTP methods and route registration order.
 * @since 1.0.0
 */
final class Specificity {

    /**
     * ### Compares route path specificity
     *
     * Compares two route patterns and determines which one has a higher matching priority.
     *
     * Static segments take precedence over dynamic parameter segments.
     *
     * When all compared segments have equal specificity, the pattern containing more segments is considered more
     * specific.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Router\Matcher\Specificity;
     * use FireHub\Adapter\HTTP\Router\Path\Pattern;
     *
     * $static = new Pattern('/users/create');
     * $dynamic = new Pattern('/users/{id}');
     *
     * Specificity::compare($static, $dynamic);
     * // 1
     *
     * Specificity::compare($dynamic, $static);
     * // -1
     *
     * Specificity::compare($static, $static);
     * // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\Path\Pattern::segments() To get route path segments.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count path segments.
     *
     * @param \FireHub\Adapter\HTTP\Router\Path\Pattern $first <p>
     * First route path pattern.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\Path\Pattern $second <p>
     * Second route path pattern.
     * </p>
     *
     * @return int<-1, 1> Positive if the first pattern is more specific, negative if the second is more specific, or
     * zero if both have equal specificity.
     */
    public static function compare (Pattern $first, Pattern $second):int {

        $first_segments = $first->segments();
        $second_segments = $second->segments();

        foreach ($first_segments as $index => $segment) {

            $other = $second_segments[$index] ?? null;

            if ($other === null) return 1;

            $first_dynamic = $segment[0] === '{';
            $second_dynamic = $other[0] === '{';

            if ($first_dynamic === $second_dynamic) continue;

            return $first_dynamic ? -1 : 1;

        }

        if (
            Runtime\Arr\Inspection::count($first_segments)
            < Runtime\Arr\Inspection::count($second_segments)
        ) return -1;

        return 0;

    }

}