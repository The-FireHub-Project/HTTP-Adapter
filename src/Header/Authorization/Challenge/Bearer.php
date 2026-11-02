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

namespace FireHub\Adapter\HTTP\Header\Authorization\Challenge;

use FireHub\Adapter\HTTP\Header\Authorization\ {
    Challenge\Bearer\Error, Challenge
};
use FireHub\Foundation\Uri;
use FireHub\Adapter\HTTP\Header\Exception\InvalidAuthenticationParameterException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Bearer authentication challenge
 *
 * Advertises Bearer authentication and optional OAuth 2.0 error information.
 *
 * Implements the WWW-Authenticate challenge syntax defined by RFC 6750.
 * @since 1.0.0
 */
final readonly class Bearer implements Challenge {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the authentication parameter syntax.
     *
     * @param null|string $realm <p>
     * Authentication protection space.
     * </p>
     * @param null|string $scope <p>
     * Required authorization scopes.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Authorization\Challenge\Bearer\Error $error <p>
     * Bearer authentication error.
     * </p>
     * @param null|string $error_description <p>
     * Human-readable error description.
     * </p>
     * @param null|\FireHub\Foundation\Uri $error_uri <p>
     * URI identifying error documentation.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidAuthenticationParameterException If the authentication
     * parameter is invalid.
     *
     * @return void
     */
    public function __construct (
        private ?string $realm = null,
        private ?string $scope = null,
        private ?Error $error = null,
        private ?string $error_description = null,
        private ?Uri $error_uri = null
    ) {

        if (
            $realm !== null
            && Runtime\Str\SB\Regex::match(
                '/[\x00-\x1F\x7F]/',
                $realm
            )
        ) throw new InvalidAuthenticationParameterException(
            'Invalid Bearer authentication realm.'
        );

        if (
            $scope !== null
            && !Runtime\Str\SB\Regex::match(
                '/\A[\x21\x23-\x5B\x5D-\x7E]+(?: [\x21\x23-\x5B\x5D-\x7E]+)*\z/',
                $scope
            )
        ) throw new InvalidAuthenticationParameterException(
            'Invalid Bearer authentication scope.'
        );

        if (
            $error_description !== null
            && !Runtime\Str\SB\Regex::match(
                '/\A[\x20-\x21\x23-\x5B\x5D-\x7E]*\z/',
                $error_description
            )
        ) throw new InvalidAuthenticationParameterException(
            'Invalid Bearer authentication error description.'
        );

        if (
            $error_uri !== null
            && !Runtime\Str\SB\Regex::match(
                '/\A[\x21\x23-\x5B\x5D-\x7E]+\z/',
                $error_uri->value()
            )
        ) throw new InvalidAuthenticationParameterException(
            'Invalid Bearer authentication error URI.'
        );

    }

    /**
     * ### Gets the serialized Bearer authentication challenge
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Authorization\Challenge\Bearer::escape() To escape the authentication
     * parameter values.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join authentication parameters.
     *
     * @return non-empty-string Serialized authentication challenge.
     */
    public function value ():string {

        $parameters = [];

        if ($this->realm !== null)
            $parameters[] = 'realm="' . $this->escape($this->realm) . '"';

        if ($this->scope !== null)
            $parameters[] = 'scope="' . $this->escape($this->scope) . '"';

        if ($this->error !== null)
            $parameters[] = 'error="' . $this->error->value . '"';

        if ($this->error_description !== null)
            $parameters[] = 'error_description="'
                . $this->escape($this->error_description) . '"';

        if ($this->error_uri !== null)
            $parameters[] = 'error_uri="'
                . $this->escape($this->error_uri->value()) . '"';

        if ($parameters === [])
            return 'Bearer';

        return 'Bearer ' . Runtime\Str\SB\Delimiter::implode($parameters, ', ');

    }

    /**
     * ### Escapes an HTTP quoted-string value
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Escape::addCSlashes() To escape the value.
     *
     * @param string $value Parameter value.
     *
     * @return string Escaped value.
     */
    private function escape (string $value):string {

        return Runtime\Str\SB\Escape::addCSlashes($value, "\\\"");

    }

}