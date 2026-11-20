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

namespace FireHub\Adapter\HTTP\Header\Cookie;

use FireHub\Adapter\HTTP\Header\{
    Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie SameSite Attribute
 *
 * Represents the SameSite attribute of an HTTP Set-Cookie header.
 *
 * The SameSite attribute controls whether cookies are included in cross-site requests.
 *
 * Supported policies:
 *
 * - Strict: Restricts cookies to same-site requests.
 * - Lax: Allows same-site requests and certain cross-site
 *   top-level navigations.
 * - None: Allows cross-site requests when Secure is enabled.
 *
 * The SameSite=None policy requires the cookie to have the Secure attribute. This constraint must be enforced by the
 * Set-Cookie representation.
 *
 * <code>
 * $sameSite = new SameSite(SameSitePolicy::LAX);
 *
 * $sameSite->value(); // SameSite=Lax
 * $sameSite->policy(); // SameSitePolicy::LAX
 *
 * $sameSite = SameSite::parse('samesite=STRICT');
 *
 * $sameSite->value(); // SameSite=Strict
 * </code>
 *
 * @since 1.0.0
 */
final readonly class SameSite extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\SameSitePolicy $policy [optional] <p>
     * Cookie SameSite policy.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private SameSitePolicy $policy = SameSitePolicy::LAX
    ) {

        parent::__construct(
            new Name('SameSite'),
            new Value($policy->value)
        );

    }

    /**
     * ### Parses an HTTP Cookie SameSite Attribute
     *
     * Parses a serialized SameSite attribute.
     *
     * The attribute name and policy value are matched case-insensitively.
     *
     * <code>
     * $sameSite = SameSite::parse('SameSite=Strict');
     *
     * $sameSite->policy(); // SameSitePolicy::STRICT
     *
     * $sameSite = SameSite::parse('samesite=none');
     *
     * $sameSite->value(); // SameSite=None
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the parameter name.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the policy value.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP cookie SameSite attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the attribute name or policy is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP cookie SameSite attribute.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('SameSite')))
            throw new InvalidParameterException(
                'Expected an HTTP cookie attribute named "SameSite".'
            );

        $content = $parameter->content()->value();

        foreach (SameSitePolicy::cases() as $policy)
            if (Runtime\Str\SB\Compare::lexical($content, $policy->value, false) === 0)
                return new self($policy);

        throw new InvalidParameterException(
            'HTTP cookie SameSite must be Strict, Lax, or None.'
        );

    }

    /**
     * ### Gets the Cookie SameSite Policy
     *
     * Returns the configured SameSite policy.
     *
     * <code>
     * $sameSite = new SameSite(SameSitePolicy::STRICT);
     *
     * $sameSite->policy(); // SameSitePolicy::STRICT
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie\SameSitePolicy
     * Cookie SameSite policy.
     */
    public function policy ():SameSitePolicy {

        return $this->policy;

    }

}