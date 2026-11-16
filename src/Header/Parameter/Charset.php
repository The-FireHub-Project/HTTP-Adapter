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

use FireHub\Core\Type\Str\Encoding;
use FireHub\Adapter\HTTP\Header\ {
    Name, Value, Parameter
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Charset Parameter
 *
 * Represents a charset parameter restricted to supported FireHub character encodings.
 *
 * <code>
 * $charset = Charset::of(Encoding::UTF_8);
 *
 * $charset->value(); // charset=UTF-8
 *
 * $charset = Charset::parse('charset=utf-8');
 *
 * $charset->encoding(); // Encoding::UTF_8
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Charset extends Parameter {

    /**
     * ### Creates a Charset Parameter
     *
     * <code>
     * $charset = Charset::of(Encoding::UTF_8);
     *
     * $charset->value(); // charset=UTF-8
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str\Encoding $encoding <p>
     * Supported character encoding.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self New charset parameter.
     */
    public static function of (Encoding $encoding):self {

        return new self(
            new Name('charset'),
            new Value($encoding->value)
        );

    }

    /**
     * ### Parses a Charset Parameter
     *
     * Resolves the charset name against supported FireHub encodings. Charset names are compared case-insensitively.
     *
     * <code>
     * $charset = Charset::parse('charset=utf-8');
     *
     * $charset->encoding(); // Encoding::UTF_8
     * $charset->value();    // charset=UTF-8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Charset::of() To create a charset parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To compare parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare parameter names.
     * @uses \FireHub\Core\Type\Str\Encoding::from() To resolve the character encoding.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To compare parameter names.
     *
     * @param non-empty-string $value <p>
     * Parameter value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed charset parameter.
     */
    public static function parse (string $value):self {

        $parameter = Parameter::parse($value);

        if (!$parameter->name()->equals(new Name('charset')))
            throw new InvalidParameterException(
                'Expected a charset parameter.'
            );

        $charset = $parameter->content()->value();

        foreach (Encoding::cases() as $encoding)
            if (Runtime\Str\SB\Casing::toLower($encoding->value) === Runtime\Str\SB\Casing::toLower($charset))
                return self::of($encoding);

        throw new InvalidParameterException(
            'Unsupported HTTP charset.'
        );

    }

    /**
     * ### Gets the Character Encoding
     *
     * <code>
     * $charset = Charset::of(Encoding::UTF_8);
     *
     * $charset->encoding(); // Encoding::UTF_8
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\Str\Encoding Character encoding.
     */
    public function encoding ():Encoding {

        return Encoding::from($this->content()->value());

    }

}