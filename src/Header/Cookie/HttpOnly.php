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

use FireHub\Adapter\HTTP\Header\Name;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie HttpOnly Attribute
 *
 * Represents the HttpOnly flag of an HTTP Set-Cookie header.
 *
 * The HttpOnly attribute prevents client-side scripts from accessing the cookie through APIs such as Document.cookie.
 *
 * It does not prevent the browser from sending the cookie with applicable HTTP requests.
 *
 * <code>
 * $httpOnly = new HttpOnly();
 *
 * $httpOnly->value(); // HttpOnly
 *
 * $httpOnly = HttpOnly::parse('httponly');
 *
 * $httpOnly->value(); // HttpOnly
 * </code>
 *
 * @since 1.0.0
 */
final readonly class HttpOnly extends Name {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct () {

        parent::__construct('HttpOnly');

    }

    /**
     * ### Parses an HTTP Cookie HttpOnly Attribute
     *
     * Parses the HttpOnly attribute using case-insensitive comparison.
     *
     * <code>
     * $httpOnly = HttpOnly::parse('HTTPONLY');
     *
     * $httpOnly->value(); // HttpOnly
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the attribute name.
     *
     * @param non-empty-string $value <p>
     * Serialized HttpOnly attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the attribute name is invalid.
     *
     * @return self Parsed HttpOnly attribute.
     */
    public static function parse (string $value):self {

        if (Runtime\Str\SB\Compare::lexical($value, 'HttpOnly', false) !== 0)
            throw new InvalidParameterException(
                'Expected an HTTP cookie attribute named "HttpOnly".'
            );

        return new self;

    }

}