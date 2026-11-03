<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Source;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException;
use FireHub\Runtime;

/**
 * ### Represents a CSP nonce source expression
 *
 * Defines a nonce used to authorize specific inline scripts or styles under a Content Security Policy.
 *
 * Nonces should be unpredictable and generated separately for each protected response.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Nonce extends ValueObject implements SourceExpression {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the nonce.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the nonce syntax.
     *
     * @param non-empty-string $nonce <p>
     * Base64-encoded nonce value without the nonce- prefix.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException If the nonce is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $nonce
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/^[A-Za-z0-9+\/_-]+={0,2}$/D',
                    $nonce
                ),
            fn() => new InvalidCSPSourceException
        );

    }

    /**
     * ### Generates a cryptographically secure nonce
     *
     * Generates a nonce using a cryptographically secure random byte generator.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Encoding::base64Encode() To encode the random bytes as Base64.
     * @uses \FireHub\Runtime\Random::bytes() To generate random bytes.
     *
     * @param positive-int $bytes [optional] <p>
     * Number of random bytes to generate.
     *
     * Defaults to 16 bytes, providing 128 bits of entropy.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException If the byte length is less than one.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Generated nonce source.
     */
    public static function generate (int $bytes = 16):self {

        if ($bytes < 1)
            throw new InvalidCSPSourceException(
                'Nonce byte length must be greater than zero.'
            );

        /** @var non-empty-string $nonce */
        $nonce = Runtime\Encoding::base64Encode(Runtime\Random::bytes($bytes));

        return new self($nonce);

    }

    /**
     * ### Gets the nonce value
     *
     * Returns the raw Base64-encoded nonce without the CSP nonce-prefix.
     * @since 1.0.0
     *
     * @return non-empty-string Nonce value.
     */
    public function nonce ():string {

        return $this->nonce;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized CSP nonce source.
     */
    public function value ():string {

        return "'nonce-".$this->nonce."'";

    }

}