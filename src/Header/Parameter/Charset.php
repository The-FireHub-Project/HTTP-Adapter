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
use FireHub\Adapter\HTTP\Header\{
    Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Charset Parameter
 *
 * Represents a charset parameter with the fixed name "charset".
 *
 * Supports known FireHub encodings and custom charset identifiers.
 *
 * Both token and quoted-string representations are accepted.
 *
 * <code>
 * $charset = new Charset(Encoding::UTF_8);
 *
 * $charset->value(); // charset=UTF-8
 *
 * $charset = Charset::parse('charset="UTF-8"');
 *
 * $charset->content()->value(); // UTF-8
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Charset extends Quoted {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the character encoding identifier.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the character encoding identifier.
     *
     * @param \FireHub\Core\Type\Str\Encoding|string $encoding <p>
     * Character encoding identifier.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (Encoding|string $encoding) {

        $value = $encoding instanceof Encoding
            ? $encoding->value
            : $encoding;

        $this->guard(
            static fn():bool => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
                $value
            ),
            static fn() => new InvalidParameterException(
                'Invalid HTTP charset identifier.'
            )
        );

        parent::__construct(
            new Name('charset'),
            new Value($value)
        );

    }

    /**
     * ### Parses an HTTP Charset Parameter
     *
     * Parses a serialized charset parameter.
     *
     * The parameter name is matched case-insensitively.
     *
     * <code>
     * $charset = Charset::parse('charset=UTF-8');
     *
     * $charset->content()->value(); // UTF-8
     *
     * $charset = Charset::parse('CHARSET=windows-1252');
     *
     * $charset->value(); // charset=windows-1252
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse()
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase()
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP charset parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed charset parameter.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('charset')))
            throw new InvalidParameterException(
                'Expected an HTTP parameter named "charset".'
            );

        return new self(
            $parameter->content()->value()
        );

    }

    /**
     * ### Gets the Known FireHub Encoding
     *
     * Resolves the charset identifier to a known FireHub encoding.
     *
     * The comparison is case-insensitive.
     *
     * Returns null when the identifier is not represented by the
     * Core Encoding enumeration.
     *
     * <code>
     * $charset = new Charset('utf-8');
     *
     * $charset->encoding(); // Encoding::UTF_8
     *
     * $charset = new Charset('custom-encoding');
     *
     * $charset->encoding(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the identifier to lowercase.
     *
     * @return null|\FireHub\Core\Type\Str\Encoding Known encoding or null.
     */
    public function encoding ():?Encoding {

        foreach (Encoding::cases() as $encoding)
            if (Runtime\Str\SB\Casing::toLower($encoding->value) === Runtime\Str\SB\Casing::toLower(
                    $this->content()->value()
                )
            ) return $encoding;

        return null;

    }

}