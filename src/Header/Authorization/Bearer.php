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
 * ### Represents HTTP Bearer authentication credentials
 *
 * Encapsulates an access token and serializes it according to the HTTP Bearer authentication scheme.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Bearer extends ValueObject implements Credentials {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the basic authentication credentials.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the basic authentication credentials syntax.
     *
     * @param non-empty-string $token <p>
     * Access token.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCredentialsFormatException If the credentials are invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $token
    ) {

        $this->guard(
            fn() => !Runtime\Str\SB\Regex::match(
                '/\A[A-Za-z0-9\-._~+\/]+=*\z/',
                $token
            ),
            fn() => new InvalidCredentialsFormatException(
                'Invalid HTTP Bearer token.'
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return "Bearer $this->token";

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected function comparisonValue ():string {

        return $this->token;

    }

}