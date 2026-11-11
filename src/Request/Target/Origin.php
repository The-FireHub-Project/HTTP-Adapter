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

namespace FireHub\Adapter\HTTP\Request\Target;

use FireHub\Adapter\HTTP\Request\Target;
use FireHub\Foundation\Uri\Component\ {
    Path, Query
};
use FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException;

/**
 * ### Represents an HTTP origin-form request target
 *
 * Contains an absolute URI path and an optional query component.
 *
 * Origin-form is the most common HTTP request-target representation.
 * @since 1.0.0
 */
final readonly class Origin implements Target {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Path::isAbsolute() To validate the path is absolute.
     * @uses \FireHub\Foundation\Uri\Component\Path::isEmpty() To validate the path is empty.
     *
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * Absolute URI path.
     * </p>
     * @param null|\FireHub\Foundation\Uri\Component\Query<string> $query [optional] <p>
     * Optional URI query.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the path is not absolute.
     *
     * @return void
     */
    public function __construct (
        private Path $path,
        private ?Query $query = null
    ) {

        if (!$path->isAbsolute() && !$path->isEmpty())
            throw new InvalidTargetException(
                'Origin-form request target requires an absolute path.'
            );

    }

    /**
     * ### Gets the URI path
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Path<string> URI path.
     */
    public function path ():Path {

        return $this->path;

    }

    /**
     * ### Gets the URI query
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\Query<string> URI query.
     */
    public function query ():?Query {

        return $this->query;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Path::isEmpty() To check if the path is empty.
     * @uses \FireHub\Foundation\Uri\Component\Path::value() To get the path value.
     * @uses \FireHub\Foundation\Uri\Component\Query::value() To get the query value.
     */
    public function value ():string {

        $target = $this->path->isEmpty()
            ? '/' : $this->path->value();

        if ($this->query !== null)
            $target .= '?'.$this->query->value();

        /** @var non-empty-string $target */
        return $target;

    }

}