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
 * ### HTTP Cookie Partitioned Attribute
 *
 * Represents the Partitioned flag of an HTTP Set-Cookie header.
 *
 * The Partitioned attribute requests that the browser store the cookie  in partitioned storage, isolated by the
 * top-level site.
 *
 * Partitioned cookies must also include the Secure attribute.
 *
 * This requirement is validated by the Set-Cookie representation rather than by the Partitioned attribute itself.
 *
 * <code>
 * $partitioned = new Partitioned();
 *
 * $partitioned->value(); // Partitioned
 *
 * $partitioned = Partitioned::parse('partitioned');
 *
 * $partitioned->value(); // Partitioned
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Partitioned extends Name {

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

        parent::__construct('Partitioned');

    }

    /**
     * ### Parses an HTTP Cookie Partitioned Attribute
     *
     * Parses the Partitioned attribute using case-insensitive comparison.
     *
     * <code>
     * $partitioned = Partitioned::parse('PARTITIONED');
     *
     * $partitioned->value(); // Partitioned
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the attribute name.
     *
     * @param non-empty-string $value <p>
     * Serialized Partitioned attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the attribute name is invalid.
     *
     * @return self Parsed Partitioned attribute.
     */
    public static function parse (string $value):self {

        if (Runtime\Str\SB\Compare::lexical($value, 'Partitioned', false) !== 0)
            throw new InvalidParameterException(
                'Expected an HTTP cookie attribute named "Partitioned".'
            );

        return new self;

    }

}