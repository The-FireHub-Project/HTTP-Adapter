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
 * ### HTTP Cookie Secure Attribute
 *
 * Represents the Secure flag of an HTTP Set-Cookie header.
 *
 * <code>
 * $secure = new Secure();
 *
 * $secure->value(); // Secure
 *
 * $secure = Secure::parse('secure');
 *
 * $secure->value(); // Secure
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Secure extends Name {

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

        parent::__construct('Secure');

    }

    /**
     * ### Parses an HTTP Cookie Secure Attribute
     *
     * <code>
     * $secure = Secure::parse('SECURE');
     *
     * $secure->value(); // Secure
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the attribute name.
     *
     * @param non-empty-string $value <p>
     * Serialized Secure attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the attribute name is invalid.
     *
     * @return self Parsed Secure attribute.
     */
    public static function parse (string $value):self {

        if (Runtime\Str\SB\Compare::lexical($value, 'Secure', false) !== 0)
            throw new InvalidParameterException(
                'Expected an HTTP cookie attribute named "Secure".'
            );

        return new self;

    }

}