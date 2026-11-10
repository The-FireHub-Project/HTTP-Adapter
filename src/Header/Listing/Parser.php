<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Listing;

use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException;
use FireHub\Runtime;

/**
 * ### Parses comma-separated HTTP header field values
 *
 * Splits HTTP header field values into individual list members.
 *
 * Commas inside quoted strings are preserved, including quoted strings containing escaped characters.
 *
 * This parser handles list syntax only. Header-specific validation remains the responsibility of the corresponding
 * header parser.
 * @since 1.0.0
 */
final class Parser {

    /**
     * ### Parses HTTP list members
     *
     * <code>
     * Parser::parse('text/html, application/json;q=0.9');
     *
     * // ['text/html', 'application/json;q=0.9']
     *
     * Parser::parse('text/plain;title="Hello, FireHub", application/json');
     *
     * // ['text/plain;title="Hello, FireHub"', 'application/json']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the input length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract list members.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     *
     * @param string $value <p>
     * Raw comma-separated HTTP header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If the list syntax is invalid.
     *
     * @return list<non-empty-string> Parsed list members.
     */
    public static function parse (string $value):array {

        $length = Runtime\Str\SB\Inspection::length($value);

        $start = 0;
        $quoted = false;
        $escaped = false;
        $members = [];

        for ($position = 0; $position < $length; $position++) {

            $character = $value[$position];

            if ($escaped) {

                $escaped = false;

                continue;

            }

            if ($quoted && $character === '\\') {

                $escaped = true;

                continue;

            }

            if ($character === '"') {

                $quoted = !$quoted;

                continue;

            }

            if ($character !== ',' || $quoted)
                continue;

            $member = Runtime\Str\SB\Transform::trim(
                Runtime\Str\SB\Access::part(
                    $value,
                    $start,
                    $position - $start
                ),
                characters: " \t"
            );

            if ($member === '')
                throw new InvalidParameterValueException(
                    'Empty HTTP header list member.'
                );

            $members[] = $member;

            $start = $position + 1;

        }

        if ($quoted || $escaped)
            throw new InvalidParameterValueException(
                'Unterminated HTTP quoted-string.'
            );

        $member = Runtime\Str\SB\Transform::trim(
            Runtime\Str\SB\Access::part(
                $value,
                $start
            ),
            characters: " \t"
        );

        if ($member === '') {

            if ($members !== [])
                throw new InvalidParameterValueException(
                    'Trailing comma in HTTP header list.'
                );

            return [];

        }

        $members[] = $member;

        return $members;

    }

}