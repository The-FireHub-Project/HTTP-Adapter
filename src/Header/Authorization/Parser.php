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

namespace FireHub\Adapter\HTTP\Header\Authorization;

use FireHub\Adapter\HTTP\Header\Exception\InvalidCredentialsFormatException;
use FireHub\Runtime;

/**
 * ### Parses HTTP Authorization header credentials
 *
 * Converts serialized HTTP Authorization header values into strongly typed authentication credentials.
 *
 * Supports Basic and Bearer authentication schemes. Authentication scheme names are matched case-insensitively.
 *
 * This parser validates the syntax of authentication credentials but does not authenticate users or verify access
 * tokens.
 * @since 1.0.0
 */
final class Parser {

    /**
     * ### Parses an HTTP Authorization header value
     *
     * Identifies the authentication scheme and converts its credentials into the corresponding authentication
     * credentials object.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the Authorization header syntax.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the authentication scheme.
     * @uses \FireHub\Adapter\HTTP\Header\Authorization\Parser::parseBasic() To parse Basic authentication.
     *
     * @param string $value <p>
     * Serialized HTTP Authorization header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCredentialsFormatException If the Authorization header
     * is malformed or uses an unsupported authentication scheme.
     * @throws \FireHub\Core\Exception\FireHubException If a credential validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a credential validation exception is not a
     * FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Authorization\Credentials Parsed authentication credentials.
     */
    public static function parse (string $value):Credentials {

        if (!Runtime\Str\SB\Regex::match(
            "/\A([!#$%&'*+.^_`|~0-9A-Za-z-]+)[ \t]+(\S(?:.*\S)?)\z/",
            $value
        )) throw new InvalidCredentialsFormatException(
            'Invalid Authorization header format.'
        );

        /**
         * @var non-empty-string $scheme
         * @var non-empty-string $credentials
         */
        [$scheme, $credentials] = Runtime\Str\SB\Delimiter::explode( // @phpstan-ignore-line
            Runtime\Str\SB\Transform::trim($value, characters: " \t"),
            ' ',
            2
        );

        $scheme = Runtime\Str\SB\Casing::toLower($scheme);

        return match ($scheme) {

            'basic' => self::parseBasic($credentials),

            'bearer' => new Bearer($credentials),

            default => throw new InvalidCredentialsFormatException(
                'Unsupported Authorization authentication scheme.'
            )

        };

    }

    /**
     * ### Parses HTTP Basic authentication credentials
     *
     * Decodes Base64 credentials and separates the username and password using the first colon character.
     *
     * Additional colon characters are preserved as part of the password.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the Base64 encoding syntax.
     * @uses \FireHub\Runtime\Encoding::base64Decode() To decode the Base64 credentials.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the first colon character.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the username and password.
     *
     * @param string $credentials <p>
     * Base64-encoded Basic authentication credentials.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCredentialsFormatException If the Basic authentication
     * credentials are malformed.
     * @throws \FireHub\Core\Exception\FireHubException If a credential validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a credential validation exception is not a
     * FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Authorization\Basic Parsed Basic authentication credentials.
     */
    private static function parseBasic (string $credentials):Basic {

        if (!Runtime\Str\SB\Regex::match(
            '/\A(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?\z/',
            $credentials
        )) throw new InvalidCredentialsFormatException(
            'Invalid Basic authentication encoding.'
        );

        $decoded = Runtime\Encoding::base64Decode($credentials);

        if ($decoded === false)
            throw new InvalidCredentialsFormatException(
                'Unable to decode Basic authentication credentials.'
            );

        $separator = Runtime\Str\SB\Search::firstPosition(':', $decoded);

        if ($separator === false)
            throw new InvalidCredentialsFormatException(
                'Basic authentication credentials must contain a colon.'
            );

        return new Basic(
            Runtime\Str\SB\Access::part($decoded, 0, $separator),
            Runtime\Str\SB\Access::part($decoded, $separator + 1)
        );

    }

}