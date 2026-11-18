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

namespace FireHub\Adapter\HTTP\Header\Parsing;

use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Header Value Splitter
 *
 * Splits HTTP header values using a specified delimiter while respecting quoted strings and escaped characters.
 *
 * Delimiters inside quoted strings are preserved. Escaped quotation marks do not terminate quoted strings.
 *
 * The splitter preserves whitespace and empty segments. Interpretation and validation of individual segments belong
 * to the caller.
 * @since 1.0.0
 */
final class Splitter {

    /**
     * ### Splits an HTTP Header Value
     *
     * Splits a header value by the specified delimiter, ignoring delimiters enclosed in quoted strings.
     *
     * Backslash escaping is recognized only inside quoted strings.
     *
     * The original content of each segment is preserved.
     *
     * <code>
     * Splitter::split('a=1;b=2', ';');
     * // ['a=1', 'b=2']
     *
     * Splitter::split('a="1;2";b=3', ';');
     * // ['a="1;2"', 'b=3']
     *
     * Splitter::split('text/html, application/json', ',');
     * // ['text/html', ' application/json']
     *
     * Splitter::split('a="1,2", b=3', ',');
     * // ['a="1,2"', ' b=3']
     *
     * Splitter::split('a=1;;b=2', ';');
     * // ['a=1', '', 'b=2']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the string length.
     *
     * @param string $value <p>
     * HTTP header value to split.
     * </p>
     * @param non-empty-string $delimiter <p>
     * Single-byte delimiter used to separate segments.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a quoted string is unterminated.
     *
     * @return list<string> Split header value segments.
     */
    public static function split (string $value, string $delimiter):array {

        if (Runtime\Str\SB\Inspection::length($delimiter) !== 1)
            throw new InvalidFieldValueException(
                'The HTTP header delimiter must be a single byte.'
            );

        $parts = []; $buffer = ''; $quoted = false; $escaped = false;
        $length = Runtime\Str\SB\Inspection::length($value);

        for ($index = 0; $index < $length; $index++) {

            $character = $value[$index];

            if ($escaped) {

                $buffer .= $character;
                $escaped = false;

                continue;

            }

            if ($quoted && $character === '\\') {

                $buffer .= $character;
                $escaped = true;

                continue;

            }

            if ($character === '"') {

                $quoted = !$quoted;
                $buffer .= $character;

                continue;

            }

            if ($character === $delimiter && !$quoted) {

                $parts[] = $buffer;
                $buffer = '';

                continue;

            }

            $buffer .= $character;

        }

        if ($quoted || $escaped)
            throw new InvalidFieldValueException(
                'Unterminated quoted string in HTTP header value.'
            );

        $parts[] = $buffer;

        return $parts;

    }

}