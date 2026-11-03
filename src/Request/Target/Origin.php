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
use FireHub\Foundation\Uri;
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
     * ### Creates an origin-form target from a URI
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::scheme() To validate the URI scheme.
     * @uses \FireHub\Foundation\Uri::authority() To validate the URI authority.
     * @uses \FireHub\Foundation\Uri::fragment() To validate the URI fragment.
     * @uses \FireHub\Foundation\Uri::path() To retrieve the URI path.
     * @uses \FireHub\Foundation\Uri::query() To retrieve the URI query.
     *
     * @param \FireHub\Foundation\Uri $uri <p>
     * URI containing the path and query.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the URI contains unsupported
     * components.
     *
     * @return self Origin-form request target.
     */
    public static function fromUri (Uri $uri):self {

        if (
            $uri->scheme() !== null
            || $uri->authority() !== null
            || $uri->fragment() !== null
        ) throw new InvalidTargetException(
            'Origin-form target cannot contain a scheme, authority, or fragment.'
        );

        return new self(
            $uri->path(),
            $uri->query()
        );

    }

    /**
     * ### Parses an origin-form request target
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Target\Origin::fromUri() To create the target from the URI.
     * @uses \FireHub\Foundation\Uri::parse() To parse the URI.
     *
     * @param string $target <p>
     * Serialized origin-form request target.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the URI contains unsupported
     * components.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidFragmentException If fragment has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed origin-form target.
     */
    public static function parse (string $target):self {

        return self::fromUri(
            Uri::parse($target)
        );

    }

    /**
     * ### Returns a target with a different path
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path <p>
     * New URI path.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the path is not absolute.
     *
     * @return self New origin-form target.
     */
    public function withPath (Path $path):self {

        return new self(
            $path,
            $this->query
        );

    }

    /**
     * ### Returns a target with a different query
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Uri\Component\Query<string> $query <p>
     * New URI query.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the path is not absolute.
     *
     * @return self New origin-form target.
     */
    public function withQuery (Query $query):self {

        return new self(
            $this->path,
            $query
        );

    }

    /**
     * ### Returns a target without a query
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the path is not absolute.
     *
     * @return self New origin-form target.
     */
    public function withoutQuery ():self {

        return new self(
            $this->path
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
     * ### Converts the target into a URI
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI component combination is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Foundation\Uri URI reference.
     */
    public function uri ():Uri {

        return new Uri(
            path: $this->path,
            query: $this->query
        );

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