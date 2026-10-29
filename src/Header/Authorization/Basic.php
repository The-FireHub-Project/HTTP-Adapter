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

namespace FireHub\Adapter\HTTP\Header\Authorization;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCredentialsFormatException;
use FireHub\Runtime;

/**
 * ### Represents HTTP Basic authentication credentials
 *
 * Encapsulates a username and password and serializes them according to the HTTP Basic authentication scheme.
 *
 * Basic authentication uses Base64 encoding, which does not provide encryption. Credentials should only be
 * transmitted over HTTPS.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Basic extends ValueObject implements Credentials {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the basic authentication credentials.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the basic authentication credentials syntax.
     *
     * @param string $username <p>
     * Username.
     * </p>
     * @param string $password <p>
     * Password.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCredentialsFormatException If the credentials are invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $username,
        private string $password
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/[\x00-\x1F\x7F]/',
                $username
            ),
            fn() => new InvalidCredentialsFormatException(
                'Username contains control characters.'
            )
        );

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/[\x00-\x1F\x7F]/',
                $password
            ),
            fn() => new InvalidCredentialsFormatException(
                'Password contains control characters.'
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Encoding::base64Encode() To encode the username and password.
     */
    public function value ():string {

        return 'Basic '.Runtime\Encoding::base64Encode(
                "$this->username:$this->password"
            );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{string, string} Comparison value.
     */
    protected function comparisonValue ():array {

        return [
            $this->username,
            $this->password
        ];

    }

}