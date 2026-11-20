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

namespace FireHub\Adapter\HTTP\Header\Parameter;

use FireHub\Adapter\HTTP\Header\{
    Name,
    Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Multipart Boundary Parameter
 *
 * Represents a multipart boundary parameter.
 *
 * Validates RFC 2046 boundary syntax and inherits HTTP quoted-string serialization from Quoted.
 *
 * <code>
 * $boundary = new Boundary('FireHub123');
 *
 * $boundary->value(); // boundary=FireHub123
 *
 * $boundary = new Boundary('FireHub:123');
 *
 * $boundary->value(); // boundary="FireHub:123"
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Boundary extends Quoted {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the multipart boundary.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the multipart boundary.
     *
     * @param string $boundary <p>
     * Multipart boundary value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $boundary) {

        $this->guard(
            static fn():bool => Runtime\Str\SB\Regex::match(
                "/\A[0-9A-Za-z'()+_,.\/:=? -]{0,69}[0-9A-Za-z'()+_,.\/:=?-]\z/",
                $boundary
            ),
            static fn() => new InvalidParameterException(
                'Invalid HTTP multipart boundary.'
            )
        );

        parent::__construct(
            new Name('boundary'),
            new Value($boundary)
        );

    }

    /**
     * ### Parses an HTTP Multipart Boundary Parameter
     *
     * Accepts token and quoted-string representations.
     *
     * <code>
     * Boundary::parse('boundary=FireHub123');
     * Boundary::parse('boundary="FireHub:123"');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic HTTP parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the parameter name.
     *
     * @param non-empty-string $value <p>
     * Serialized boundary parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed boundary parameter.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('boundary')))
            throw new InvalidParameterException(
                'Expected an HTTP parameter named "boundary".'
            );

        return new self($parameter->content()->value());

    }

}