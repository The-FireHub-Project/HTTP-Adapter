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

namespace FireHub\Adapter\HTTP\Header\Connection;

use FireHub\Adapter\HTTP\Header\Listing\Parser as ListingParser;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException;
use FireHub\Runtime;

/**
 * ### Parses HTTP Connection header values
 *
 * Converts connection options into known or custom HTTP tokens.
 * @since 1.0.0
 */
final class Parser {

    /**
     * ### Parses a Connection header value
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To parse the Connection header value.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the HTTP token syntax.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the option to lowercase.
     *
     * @param string $value <p>
     * Raw Connection header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If an option is invalid.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Connection\Type|\FireHub\Adapter\HTTP\Header\Connection\Custom>
     * Parsed connection options.
     */
    public static function parse (string $value):array {

        $options = [];

        foreach (ListingParser::parse($value) as $option) {

            if (!Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/D",
                $option
            )) throw new InvalidParameterValueException(
                'Invalid HTTP Connection option.'
            );

            $normalized = Runtime\Str\SB\Casing::toLower($option);

            $type = Type::tryFrom($normalized);

            $options[] = $type ?? new Custom($option);

        }

        return $options;

    }

}