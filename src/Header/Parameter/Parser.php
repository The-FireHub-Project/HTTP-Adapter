<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Parameter;

use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException;
use FireHub\Runtime;

/**
 * ### Parses HTTP semicolon-delimited parameters
 *
 * Supports token and quoted-string values, including escaped characters inside quoted strings.
 *
 * The parser validates HTTP parameter syntax without applying header-specific semantic rules.
 * @since 1.0.0
 */
final class Parser {

    /**
     * ### Parses HTTP parameters
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Parser::skipWhitespace() To skip optional whitespace.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Parser::isTokenCharacter() To check for valid token characters.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Parser::isQuotedTextCharacter() To check for valid quoted-string
     * characters.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Parser::isQuotedPairCharacter() To check for valid quoted-string
     * escape characters.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the string length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get a substring.
     *
     * @param string $value <p>
     * Semicolon-delimited HTTP parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If the parameter syntax is invalid.
     *
     * @return list<array{name: non-empty-string, value: string,  quoted: bool}> Parsed parameters.
     */
    public static function parse (string $value):array {

        $length = Runtime\Str\SB\Inspection::length($value);
        $position = 0;
        $parameters = [];

        while ($position < $length) {

            self::skipWhitespace($value, $position, $length);

            if ($position >= $length) break;

            $start = $position;

            while (
                $position < $length
                && self::isTokenCharacter($value[$position])
            ) $position++;

            if ($position === $start)
                throw new InvalidParameterValueException(
                    'Invalid HTTP parameter name.'
                );

            $name = Runtime\Str\SB\Access::part(
                $value,
                $start,
                $position - $start
            );

            self::skipWhitespace($value, $position, $length);

            if (
                $position >= $length
                || $value[$position] !== '='
            ) throw new InvalidParameterValueException(
                'Expected equals sign after HTTP parameter name.'
            );

            $position++;

            self::skipWhitespace($value, $position, $length);

            if ($position >= $length)
                throw new InvalidParameterValueException(
                    'Missing HTTP parameter value.'
                );

            $quoted = $value[$position] === '"';

            if ($quoted) {

                $position++;
                $parsed = '';
                $closed = false;

                while ($position < $length) {

                    $character = $value[$position++];

                    if ($character === '"') {
                        $closed = true;
                        break;
                    }

                    if ($character === '\\') {

                        if ($position >= $length)
                            throw new InvalidParameterValueException(
                                'Incomplete HTTP quoted-string escape.'
                            );

                        $character = $value[$position++];

                        if (!self::isQuotedPairCharacter($character))
                            throw new InvalidParameterValueException(
                                'Invalid HTTP quoted-string escape.'
                            );

                    } elseif (!self::isQuotedTextCharacter($character)) {

                        throw new InvalidParameterValueException(
                            'Invalid HTTP quoted-string character.'
                        );

                    }

                    $parsed .= $character;

                }

                if (!$closed)
                    throw new InvalidParameterValueException(
                        'Unterminated HTTP quoted-string.'
                    );

            } else {

                $start = $position;

                while (
                    $position < $length
                    && self::isTokenCharacter($value[$position])
                ) $position++;

                if ($position === $start)
                    throw new InvalidParameterValueException(
                        'Invalid HTTP parameter value.'
                    );

                $parsed = Runtime\Str\SB\Access::part(
                    $value,
                    $start,
                    $position - $start
                );

            }

            $parameters[] = [
                'name' => $name,
                'value' => $parsed,
                'quoted' => $quoted
            ];

            self::skipWhitespace($value, $position, $length);

            if ($position >= $length)
                break;

            if ($value[$position] !== ';')
                throw new InvalidParameterValueException(
                    'Expected semicolon between HTTP parameters.'
                );

            $position++;

            self::skipWhitespace($value, $position, $length);

            if ($position >= $length)
                throw new InvalidParameterValueException(
                    'Trailing semicolon in HTTP parameters.'
                );

        }

        /** @var list<array{name: non-empty-string, value: string, quoted: bool}> */
        return $parameters;

    }

    /**
     * ### Skips HTTP optional whitespace
     * @since 1.0.0
     *
     * @param string $value <p>
     * Input string.
     * </p>
     * @param int $position <p>
     * Current position.
     * </p>
     * @param int $length <p>
     * Input length.
     * </p>
     *
     * @return void
     */
    private static function skipWhitespace (string $value, int &$position, int $length):void {

        while (
            $position < $length
            && (
                $value[$position] === ' '
                || $value[$position] === "\t"
            )
        ) $position++;

    }

    /**
     * ### Checks whether a character belongs to the HTTP token grammar
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check for valid token characters.
     *
     * @param string $character <p>
     * Single-byte character.
     * </p>
     *
     * @return bool True if the character is a valid token character.
     */
    private static function isTokenCharacter (string $character):bool {

        return Runtime\Str\SB\Regex::match(
            "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]\z/",
            $character
        );

    }

    /**
     * ### Checks whether a character is valid inside a quoted-string
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Char\SB::ord() To get the character ordinal.
     *
     * @param string $character <p>
     * Single-byte character.
     * </p>
     *
     * @return bool True if valid.
     */
    private static function isQuotedTextCharacter (string $character):bool {

        $ordinal = Runtime\Char\SB::ord($character);

        return $ordinal === 9
            || $ordinal === 32
            || $ordinal === 33
            || ($ordinal >= 35 && $ordinal <= 91)
            || ($ordinal >= 93 && $ordinal <= 126)
            || $ordinal >= 128;

    }

    /**
     * ### Checks whether a character is valid after a backslash
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Char\SB::ord() To get the character ordinal.
     *
     * @param string $character <p>
     * Single-byte character.
     * </p>
     *
     * @return bool True if valid.
     */
    private static function isQuotedPairCharacter (string $character):bool {

        $ordinal = Runtime\Char\SB::ord($character);

        return $ordinal === 9
            || ($ordinal >= 32 && $ordinal <= 126)
            || $ordinal >= 128;

    }

}