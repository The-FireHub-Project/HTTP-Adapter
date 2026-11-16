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

namespace FireHub\Adapter\HTTP\Header\Content\Type;

use FireHub\Adapter\HTTP\Header\ {
    Name, Value, Parameter
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Multipart Boundary Parameter
 *
 * Represents a MIME multipart boundary parameter.
 *
 * Boundary values contain between 1 and 70 ASCII characters and must not end with whitespace.
 *
 * Values requiring quotation marks are automatically quoted during serialization by the underlying HTTP Parameter
 * implementation.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Content\Type\Boundary;
 *
 * $boundary = Boundary::of('----FireHubBoundary123');
 *
 * $boundary->value();
 * // boundary=----FireHubBoundary123
 *
 * $boundary = Boundary::parse('boundary="example:123"');
 *
 * $boundary->boundary();
 * // example:123
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Boundary extends Parameter {

    /**
     * ### Creates a Multipart Boundary Parameter
     *
     * <code>
     * $boundary = Boundary::of('----FireHubBoundary123');
     *
     * $boundary->value();
     * // boundary=----FireHubBoundary123
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the boundary value.
     *
     * @param non-empty-string $boundary <p>
     * Multipart boundary identifier.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self New boundary parameter.
     */
    public static function of (string $boundary):self {

        if (!Runtime\Str\SB\Regex::match(
            "/\A[0-9A-Za-z'()+_,.\/:=? -]{0,69}[0-9A-Za-z'()+_,.\/:=?-]\z/",
            $boundary
        )) throw new InvalidParameterException(
            'Invalid MIME multipart boundary.'
        );

        return new self(
            new Name('boundary'),
            new Value($boundary)
        );

    }

    /**
     * ### Parses a Multipart Boundary Parameter
     *
     * Parses a serialized boundary parameter, including quoted values.
     *
     * <code>
     * $boundary = Boundary::parse(
     *     'boundary="example:123"'
     * );
     *
     * $boundary->boundary(); // example:123
     * $boundary->value();    // boundary="example:123"
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized boundary parameter.
     * </p>
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type\Boundary::of() To create the boundary parameter.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed boundary parameter.
     */
    public static function parse (string $value):self {

        $parameter = Parameter::parse($value);

        if (!$parameter->name()->equals(new Name('boundary')))
            throw new InvalidParameterException(
                'Expected a multipart boundary parameter.'
            );

        /** @var non-empty-string $boundary */
        $boundary = $parameter->content()->value();

        return self::of($boundary);

    }

    /**
     * ### Gets the Multipart Boundary
     *
     * Returns the boundary identifier without the parameter name or surrounding quotation marks.
     *
     * <code>
     * $boundary = Boundary::of('----FireHubBoundary123');
     *
     * $boundary->boundary();
     * // ----FireHubBoundary123
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Multipart boundary identifier.
     */
    public function boundary ():string {

        /** @var non-empty-string */
        return $this->content()->value();

    }

}