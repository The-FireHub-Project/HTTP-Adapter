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

namespace FireHub\Adapter\HTTP\Header\Cookie;

use FireHub\Adapter\HTTP\Header\ {
    Name, Value, Parameter
};
use FireHub\Foundation\Uri\Component\Path as UriPath;
use FireHub\Adapter\HTTP\Header\Exception\ {
    InvalidFieldValueException, InvalidParameterException
};
use FireHub\Runtime;

/**
 * ### HTTP Cookie Path Attribute
 *
 * Represents the Path attribute of an HTTP Set-Cookie header.
 *
 * Cookie paths must begin with a forward slash and cannot contain control characters or semicolons.
 *
 * <code>
 * $path = new Path('/users');
 *
 * $path->name()->value();    // Path
 * $path->content()->value(); // /users
 *
 * $path = Path::parse('Path=/users');
 *
 * $path->content()->value(); // /users
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Path extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the cookie path.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the cookie path.
     *
     * @param non-empty-string $path <p>
     * Cookie path beginning with a forward slash.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie path is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $path) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A\/[\x20-\x3A\x3C-\x7E]*\z/',
                $path
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP cookie path.'
            )
        );

        parent::__construct(
            new Name('Path'),
            new Value($path)
        );

    }

    /**
     * ### Parses an HTTP Cookie Path Attribute
     *
     * Parses a serialized Path attribute and validates its name and path value.
     *
     * <code>
     * $path = Path::parse('Path=/users');
     *
     * $path->content()->value(); // /users
     *
     * $path = Path::parse('path=/');
     *
     * $path->isRoot(); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic HTTP parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare the parameter name.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP cookie Path attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter syntax is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP cookie Path attribute.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('Path')))
            throw new InvalidParameterException(
                'Expected an HTTP cookie attribute named "Path".'
            );

        /** @var non-empty-string $path */
        $path = $parameter->content()->value();

        return new self($path);

    }

    /**
     * ### Creates a Cookie Path from a URI Path
     *
     * <code>
     * $path = Path::fromUri(
     *     new UriPath('/users')
     * );
     *
     * $path->content()->value(); // /users
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Path::value() To retrieve the URI path.
     *
     * @param \FireHub\Foundation\Uri\Component\Path<non-empty-string> $path <p>
     * Foundation URI path.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie path is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Cookie path attribute.
     */
    public static function fromUri (UriPath $path):self {

        return new self($path->value());

    }

    /**
     * ### Checks Whether the Cookie Path Is the Root
     *
     * <code>
     * (new Path('/'))->isRoot();      // true
     * (new Path('/users'))->isRoot(); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the cookie path is the root path.
     */
    public function isRoot ():bool {

        return $this->content()->value() === '/';

    }

}