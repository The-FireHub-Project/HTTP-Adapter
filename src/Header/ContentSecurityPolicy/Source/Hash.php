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

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException;
use FireHub\Runtime;

/**
 * ### Represents a CSP hash source expression
 *
 * Defines a cryptographic hash source expression used to authorize inline scripts or styles under a Content Security
 * Policy.
 *
 * Supports SHA-256, SHA-384, and SHA-512 algorithms.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Hash extends ValueObject implements SourceExpression {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the hash source.
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\HashSource::digestLength() To validate the algorithm.
     * @uses \FireHub\Runtime\Encoding::base64Encode() To validate the digest.
     * @uses \FireHub\Runtime\Encoding::base64Decode() To validate the digest.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To validate the digest length.
     *
     * @param \FireHub\Runtime\Hash\Algorithm::SHA256|\FireHub\Runtime\Hash\Algorithm::SHA384|\FireHub\Runtime\Hash\Algorithm::SHA512 $algorithm <p>
     * Cryptographic hash algorithm.
     * Only SHA-256, SHA-384, and SHA-512 are supported.
     * </p>
     * @param non-empty-string $digest <p>
     * Base64-encoded cryptographic digest.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException If the algorithm or digest is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private Runtime\Hash\Algorithm $algorithm,
        private string $digest
    ) {

        $length = self::digestLength($algorithm);

        $decoded = Runtime\Encoding::base64Decode($digest);

        $this->guard(
            fn() => $decoded !== false
                && Runtime\Str\SB\Inspection::length($decoded) === $length
                && Runtime\Encoding::base64Encode($decoded) === $digest,
            fn() => new InvalidCSPSourceException(
                'Invalid CSP hash digest.'
            )
        );

    }

    /**
     * ### Creates a hash source from content
     *
     * Computes a CSP-compatible cryptographic digest of the supplied content and encodes the resulting binary digest
     * using Base64.
     *
     * The supplied content must exactly match the content evaluated
     * by the user agent.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Hash::digestLength() To validate the algorithm.
     * @uses \FireHub\Runtime\Encoding::base64Encode() To encode the digest.
     * @uses \FireHub\Runtime\Hash::hash() To compute the digest.
     *
     * @param \FireHub\Runtime\Hash\Algorithm::SHA256|\FireHub\Runtime\Hash\Algorithm::SHA384|\FireHub\Runtime\Hash\Algorithm::SHA512 $algorithm <p>
     * Cryptographic hash algorithm.
     * Only SHA-256, SHA-384, and SHA-512 are supported.
     * </p>
     * @param string $content <p>
     * Script or style content to hash.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException If the algorithm is not supported.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Generated CSP hash source.
     */
    public static function fromContent (Runtime\Hash\Algorithm $algorithm, string $content):self {

        self::digestLength($algorithm);

        /** @var non-empty-string $digest */
        $digest = Runtime\Encoding::base64Encode(
            Runtime\Hash::hash($algorithm, $content, true)
        );

        return new self(
            $algorithm,
            $digest
        );

    }

    /**
     * ### Gets the hash algorithm
     * @since 1.0.0
     *
     * @return \FireHub\Runtime\Hash\Algorithm Cryptographic hash algorithm.
     */
    public function algorithm ():Runtime\Hash\Algorithm {

        return $this->algorithm;

    }

    /**
     * ### Gets the Base64-encoded digest
     * @since 1.0.0
     *
     * @return non-empty-string Base64-encoded digest.
     */
    public function digest ():string {

        return $this->digest;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized CSP hash source expression.
     */
    public function value ():string {

        return "'"
            .$this->algorithm->value
            .'-'
            .$this->digest
            ."'";

    }

    /**
     * ### Gets the expected digest length
     *
     * Validates that the algorithm is supported by CSP and returns its expected binary digest length.
     * @since 1.0.0
     *
     * @param \FireHub\Runtime\Hash\Algorithm::SHA256|\FireHub\Runtime\Hash\Algorithm::SHA384|\FireHub\Runtime\Hash\Algorithm::SHA512 $algorithm <p>
     * Cryptographic hash algorithm.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException If the algorithm is not supported.
     *
     * @return positive-int Expected digest length in bytes.
     */
    private static function digestLength (Runtime\Hash\Algorithm $algorithm):int {

        return match ($algorithm) {
            Runtime\Hash\Algorithm::SHA256 => 32,
            Runtime\Hash\Algorithm::SHA384 => 48,
            Runtime\Hash\Algorithm::SHA512 => 64, // @phpstan-ignore match.alwaysTrue
            default => throw new InvalidCSPSourceException(
                'Unsupported CSP hash algorithm.'
            )
        };

    }

}