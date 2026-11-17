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

use FireHub\Foundation\Uri\Component\Path;
use FireHub\Foundation\Uri\Encoding\Percent;
use FireHub\Runtime;

/**
 * ### Represents a prepared HTTP request path
 *
 * Validates and splits an incoming URI path into raw and canonical segments.
 *
 * Path preparation is performed once per routing operation.
 *
 * Raw segments preserve their original percent-encoded representation for route parameter extraction.
 *
 * Canonical segments are used exclusively for static route matching.
 * @since 1.0.0
 */
final readonly class RequestPath {

    /**
     * ### Raw path segments
     * @since 1.0.0
     *
     * @var list<string>
     */
    private array $segments;

    /**
     * ### Canonical path segments
     * @since 1.0.0
     *
     * @var list<string>
     */
    private array $canonical_segments;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param list<string> $segments <p>
     * Raw path segments.
     * </p>
     * @param list<string> $canonical_segments <p>
     * Canonical path segments.
     * </p>
     *
     * @return void
     */
    private function __construct (array $segments, array $canonical_segments) {

        $this->segments = $segments;
        $this->canonical_segments = $canonical_segments;

    }

    /**
     * ### Prepares an incoming URI path for route matching
     *
     * Splits a validated URI path into raw and canonical segments.
     *
     * Returns null when the path structure is unsupported by the router.
     *
     * Raw segments preserve percent-encoded values for parameter extraction.
     *
     * <code>
     * use FireHub\Foundation\Uri\Component\Path;
     * use FireHub\Adapter\HTTP\Router\Matcher\RequestPath;
     *
     * $path = RequestPath::parse(
     *     new Path('/users/%41lice')
     * );
     *
     * $path?->segments();
     * // ['users', '%41lice']
     *
     * $path?->canonicalSegments();
     * // ['users', 'Alice']
     *
     * RequestPath::parse(new Path('/users/'));
     * // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Path::isRoot() To check whether the path represents the root.
     * @uses \FireHub\Foundation\Uri\Component\Path::isAbsolute() To check whether the path is absolute.
     * @uses \FireHub\Foundation\Uri\Component\Path::value() To retrieve the original URI path.
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::normalize() To normalize individual URI segments.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the path segment after the first slash.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the path into segments.
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the path ends with a slash.
     *
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * Incoming URI path.
     * </p>
     *
     * @return null|self Prepared request path, or null if unsupported.
     */
    public static function parse (Path $path):?self {

        if ($path->isRoot())
            return new self([], []);

        if (!$path->isAbsolute() || $path->isEmpty())
            return null;

        $value = $path->value();

        if (Runtime\Str\SB\Search::endsWith('/', $value))
            return null;

        $segments = Runtime\Str\SB\Delimiter::explode(
            Runtime\Str\SB\Access::part($value, 1),
            '/'
        );

        /** @var list<string> $canonical_segments */
        $canonical_segments = [];

        foreach ($segments as $segment) {

            if ($segment === '') return null;

            $canonical_segments[] = Percent::normalize($segment);

        }

        return new self($segments, $canonical_segments);

    }

    /**
     * ### Gets raw URI path segments
     *
     * @since 1.0.0
     *
     * @return list<string> Raw path segments.
     */
    public function segments ():array {

        return $this->segments;

    }

    /**
     * ### Gets canonical URI path segments
     *
     * @since 1.0.0
     *
     * @return list<string> Canonical path segments.
     */
    public function canonicalSegments ():array {

        return $this->canonical_segments;

    }

}