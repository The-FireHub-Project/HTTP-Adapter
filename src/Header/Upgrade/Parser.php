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

namespace FireHub\Adapter\HTTP\Header\Upgrade;

use FireHub\Adapter\HTTP\Header\Name;
use FireHub\Adapter\HTTP\Header\Listing\Parser as ListingParser;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException;
use FireHub\Runtime;

/**
 * ### Parses HTTP Upgrade header values
 * @since 1.0.0
 */
final class Parser {

    /**
     * ### Parses an Upgrade header
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To parse the Upgrade header value.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the HTTP token syntax.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the protocol name to lowercase.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the separator between protocol name and version.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the protocol name and version.
     *
     * @param string $value <p>
     * Raw Upgrade header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If the syntax is invalid.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Upgrade\Protocol> Parsed protocols.
     */
    public static function parse (string $value):array {

        $protocols = [];

        foreach (ListingParser::parse($value) as $item) {

            if (!Runtime\Str\SB\Regex::match(
                "/\A([!#$%&'*+.^_`|~0-9A-Za-z-]+)(?:\/([!#$%&'*+.^_`|~0-9A-Za-z-]+))?\z/",
                $item
            )) throw new InvalidParameterValueException(
                'Invalid HTTP Upgrade protocol.'
            );

            $separator = Runtime\Str\SB\Search::firstPosition(
                '/',
                $item
            );

            if ($separator === false) {

                $name = $item;

                $version = null;

            } else {

                /** @var non-empty-string $name */
                $name = Runtime\Str\SB\Access::part(
                    $item,
                    0,
                    $separator
                );

                /** @var non-empty-string $new_name */
                $new_name = Runtime\Str\SB\Access::part(
                    $item,
                    $separator + 1
                );

                $version = new Name($new_name);

            }

            /** @var non-empty-string&lowercase-string $normalized */
            $normalized = Runtime\Str\SB\Casing::toLower($name);

            $type = Type::tryFrom($normalized) ?? new Custom($name);

            $protocols[] = new Protocol(
                $type,
                $version
            );

        }

        return $protocols;

    }

}