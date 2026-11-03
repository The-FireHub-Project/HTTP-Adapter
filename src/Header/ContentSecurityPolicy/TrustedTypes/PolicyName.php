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

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\TrustedTypes;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException;
use FireHub\Runtime;

/**
 * ### Trusted Types Policy Name
 *
 * Represents a valid JavaScript Trusted Types policy name.
 *
 * Policy names are used by the Content Security Policy trusted-types directive to restrict policy creation.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class PolicyName extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the policy name.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the policy name syntax.
     *
     * @param non-empty-string $name <p>
     * Trusted Types policy name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the policy name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $name
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/^[A-Za-z0-9#=_\/@.%\-]+$/D',
                $name
            ),
            fn() => new InvalidContentSecurityPolicyException(
                'Invalid Trusted Types policy name.'
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string Trusted Types policy name.
     */
    public function value ():string {

        return $this->name;

    }

}