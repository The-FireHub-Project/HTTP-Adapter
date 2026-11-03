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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidStrictTransportSecurityException;

/**
 * ### Represents an HTTP Strict-Transport-Security header
 *
 * Defines a policy requiring user agents to access the host exclusively through secure HTTPS connections.
 *
 * Supports max-age, includeSubDomains, and preload directives.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class StrictTransportSecurity extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the maximum age.
     *
     * @param non-negative-int $max_age <p></p>
     * Duration of the HSTS policy in seconds.
     * A value of zero removes the stored HSTS policy for the host.
     * </p>
     * @param bool $include_subdomains [optional] <p>
     * Whether the policy also applies to subdomains.
     * </p>
     * @param bool $preload [optional] <p>
     * Whether to include the preload directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidStrictTransportSecurityException If the maximum age is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $max_age,
        private bool $include_subdomains = false,
        private bool $preload = false
    ) {

        $this->guard(
            fn() => $max_age >= 0,
            fn() => new InvalidStrictTransportSecurityException(
                'HSTS max-age cannot be negative.'
            )
        );

    }

    /**
     * ### Returns a policy with a different maximum age
     * @since 1.0.0
     *
     * @param non-negative-int $max_age <p>
     * New policy duration in seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidStrictTransportSecurityException If the maximum age is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated HSTS policy.
     */
    public function withMaxAge (int $max_age):self {

        return new self(
            $max_age,
            $this->include_subdomains,
            $this->preload
        );

    }

    /**
     * ### Returns a policy that includes subdomains
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidStrictTransportSecurityException If the maximum age is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated HSTS policy.
     */
    public function withIncludeSubDomains ():self {

        return new self(
            $this->max_age,
            true,
            $this->preload
        );

    }

    /**
     * ### Returns a policy that excludes subdomains
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidStrictTransportSecurityException If the maximum age is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated HSTS policy.
     */
    public function withoutIncludeSubDomains ():self {

        return new self(
            $this->max_age,
            false,
            $this->preload
        );

    }

    /**
     * ### Returns a policy with the preload directive
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidStrictTransportSecurityException If the maximum age is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated HSTS policy.
     */
    public function withPreload ():self {

        return new self(
            $this->max_age,
            $this->include_subdomains,
            true
        );

    }

    /**
     * ### Returns a policy without the preload directive
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidStrictTransportSecurityException If the maximum age is negative.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated HSTS policy.
     */
    public function withoutPreload ():self {

        return new self(
            $this->max_age,
            $this->include_subdomains,
            false
        );

    }

    /**
     * ### Gets the maximum age
     * @since 1.0.0
     *
     * @return non-negative-int HSTS policy duration in seconds.
     */
    public function maxAge ():int {

        return $this->max_age;

    }

    /**
     * ### Checks whether subdomains are included
     * @since 1.0.0
     *
     * @return bool True if the policy includes subdomains.
     */
    public function includesSubDomains ():bool {

        return $this->include_subdomains;

    }

    /**
     * ### Checks whether preload is enabled
     * @since 1.0.0
     *
     * @return bool True if the preload directive is enabled.
     */
    public function isPreload ():bool {

        return $this->preload;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized HSTS header value.
     */
    public function value ():string {

        $value = 'max-age='.$this->max_age;

        if ($this->include_subdomains)
            $value .= '; includeSubDomains';

        if ($this->preload)
            $value .= '; preload';

        return $value;

    }

}