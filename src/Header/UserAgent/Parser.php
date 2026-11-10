<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\UserAgent;

use FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException;
use FireHub\Runtime;

/**
 * ### Parses HTTP User-Agent header values
 *
 * Parses products and comments according to HTTP User-Agent syntax.
 *
 * Supports nested comments and quoted-pair escaping.
 * @since 1.0.0
 */
final class Parser {

    /**
     * ### Parses a User-Agent header
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Parser::parseProduct() To parse products.
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Parser::parseComment() To parse comments.
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Parser::skipWhitespace() To skip optional whitespace.
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Parser::readToken() To read the product name and version.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the input length.
     *
     * @param string $value <p>
     * Raw User-Agent header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException If the syntax is invalid.
     *
     * @return non-empty-list<\FireHub\Adapter\HTTP\Header\UserAgent\Product|\FireHub\Adapter\HTTP\Header\UserAgent\Comment>
     * Parsed User-Agent components.
     */
    public static function parse (string $value):array {

        $length = Runtime\Str\SB\Inspection::length($value);
        $position = 0;
        $components = [];

        self::skipWhitespace($value, $position, $length);

        if ($position >= $length)
            throw new InvalidUserAgentProductException(
                'User-Agent header cannot be empty.'
            );

        $components[] = self::parseProduct(
            $value,
            $position,
            $length
        );

        while ($position < $length) {

            $before = $position;

            self::skipWhitespace($value, $position, $length);

            if ($position >= $length)
                break;

            if ($position === $before)
                throw new InvalidUserAgentProductException(
                    'User-Agent components must be separated by whitespace.'
                );

            $components[] = $value[$position] === '('
                ? self::parseComment($value, $position, $length)
                : self::parseProduct($value, $position, $length);

        }

        return $components;

    }

    /**
     * ### Parses a User-Agent product
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Parser::readToken() To read the product name and version.
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
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException If the syntax is invalid.
     *
     * @return \FireHub\Adapter\HTTP\Header\UserAgent\Product Parsed product.
     */
    private static function parseProduct (string $value, int &$position, int $length):Product {

        $name = self::readToken($value, $position, $length);

        if ($name === '')
            throw new InvalidUserAgentProductException(
                'Invalid User-Agent product name.'
            );

        $version = null;

        if (
            $position < $length
            && $value[$position] === '/'
        ) {

            $position++;

            $version = self::readToken(
                $value,
                $position,
                $length
            );

            if ($version === '')
                throw new InvalidUserAgentProductException(
                    'Invalid User-Agent product version.'
                );

        }

        return new Product($name, $version);

    }

    /**
     * ### Parses an HTTP comment
     *
     * Supports nested comments and escaped characters.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Parser::isCommentCharacter() To check whether the character is a
     * valid comment character.
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
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException If the syntax is invalid.
     *
     * @return \FireHub\Adapter\HTTP\Header\UserAgent\Comment Parsed comment.
     */
    private static function parseComment (string $value, int &$position, int $length):Comment {

        $position++;

        $depth = 1; $comment = '';
        while ($position < $length) {

            $character = $value[$position++];

            if ($character === '\\') {

                if ($position >= $length)
                    throw new InvalidUserAgentProductException(
                        'Incomplete User-Agent comment escape.'
                    );

                $escaped = $value[$position++];

                if (!self::isCommentCharacter($escaped))
                    throw new InvalidUserAgentProductException(
                        'Invalid User-Agent comment escape.'
                    );

                $comment .= $escaped;

                continue;

            }

            if ($character === '(') {

                $depth++;

                $comment .= $character;

                continue;

            }

            if ($character === ')') {

                $depth--;

                if ($depth === 0)
                    return new Comment($comment);

                $comment .= $character;

                continue;

            }

            if (!self::isCommentCharacter($character))
                throw new InvalidUserAgentProductException(
                    'Invalid User-Agent comment character.'
                );

            $comment .= $character;

        }

        throw new InvalidUserAgentProductException(
            'Unterminated User-Agent comment.'
        );

    }

    /**
     * ### Reads an HTTP token
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check whether the character is a valid token character.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get the token substring.
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
     * @return string Parsed token.
     */
    private static function readToken (string $value, int &$position, int $length):string {

        $start = $position;

        while (
            $position < $length
            && Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]\z/",
                $value[$position]
            )
        ) $position++;

        return Runtime\Str\SB\Access::part(
            $value,
            $start,
            $position - $start
        );

    }

    /**
     * ### Skips HTTP whitespace
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
     * ### Checks whether a character is allowed inside an HTTP comment
     * @since 1.0.0
     *
     * @param string $character <p>
     * Single-byte character.
     * </p>
     *
     * @return bool True if valid.
     */
    private static function isCommentCharacter (string $character):bool {

        $ordinal = Runtime\Char\SB::ord($character);

        return $ordinal === 9
            || ($ordinal >= 32 && $ordinal <= 126)
            || $ordinal >= 128;

    }

}