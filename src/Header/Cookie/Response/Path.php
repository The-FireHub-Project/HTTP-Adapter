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

namespace FireHub\Adapter\HTTP\Header\Cookie\Response;

use FireHub\Core\Type\ValueObject;
use FireHub\Foundation\Uri\Component\Path as UriPath;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie Path Attribute
 *
 * Represents the Path attribute of an HTTP Set-Cookie header.
 *
 * Cookie paths determine the URL path scope in which a browser sends the cookie.
 *
 * Cookie paths are preserved exactly as provided, without URI normalization or percent-decoding.
 *
 * <code>
 * $path = new Path('/users');
 *
 * $path->value();
 * // /users
 *
 * $path->headerValue();
 * // Path=/users
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Path extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard()
     * To validate the cookie path.
     * @uses \FireHub\Runtime\Str\SB\Regex::match()
     * To validate the path syntax.
     *
     * @param non-empty-string $path <p>
     * Cookie path.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $path
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A\/[\x20-\x3A\x3C-\x7E]*\z/',
                $path
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP cookie path.'
            )
        );

    }

    /**
     * ### Creates a Cookie Path from a URI Path
     *
     * Converts a Foundation URI path into a cookie path.
     *
     * The original percent-encoded representation is preserved.
     *
     * <code>
     * $uriPath = new UriPath('/users/profile');
     *
     * $path = Path::fromUri($uriPath);
     *
     * $path->value();
     * // /users/profile
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Path::value() To retrieve the original URI path.
     *
     * @param \FireHub\Foundation\Uri\Component\Path<non-empty-string> $path <p>
     * URI path component.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the URI path cannot be
     * represented as a cookie path.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Cookie path.
     */
    public static function fromUri (UriPath $path):self {

        return new self($path->value());

    }

    /**
     * ### Checks whether the Cookie Path is Root
     *
     * <code>
     * (new Path('/'))->isRoot();
     * // true
     *
     * (new Path('/users'))->isRoot();
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the cookie path is root.
     */
    public function isRoot ():bool {

        return $this->path === '/';

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $path = new Path('/users');
     *
     * $path->value();
     * // /users
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->path;

    }

    /**
     * ### Gets the Serialized Path Attribute
     *
     * <code>
     * $path = new Path('/users');
     *
     * $path->headerValue();
     * // Path=/users
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized Path attribute.
     */
    public function headerValue ():string {

        return 'Path='.$this->path;

    }

}