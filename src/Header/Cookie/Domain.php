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

use FireHub\Adapter\HTTP\Header\ {
    Name, Parameter, Value
};
use FireHub\Foundation\Uri\Component\Host;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie Domain Attribute
 *
 * Represents the Domain attribute of an HTTP Set-Cookie header.
 *
 * Cookie domains are restricted to DNS hostnames and are normalized to lowercase.
 *
 * The leading dot is not included in the canonical representation.
 *
 * <code>
 * $domain = new Domain('Example.COM');
 *
 * $domain->content()->value(); // example.com
 * $domain->value();            // Domain=example.com
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Domain extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the domain.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the domain.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the DNS hostname.
     *
     * @param non-empty-string $domain Cookie domain.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the domain is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $domain) {

        $normalized = Runtime\Str\SB\Casing::toLower($domain);

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A(?=.{1,253}\z)[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)*\z/',
                $normalized
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP cookie domain.'
            )
        );

        parent::__construct(
            new Name('Domain'),
            new Value($normalized)
        );

    }

    /**
     * ### Parses an HTTP Cookie Domain Attribute
     *
     * Parses a serialized Domain attribute and validates its parameter name and DNS hostname.
     *
     * <code>
     * $domain = Domain::parse('Domain=Example.COM');
     *
     * $domain->content()->value(); // example.com
     * $domain->value();            // Domain=example.com
     *
     * $domain = Domain::parse('domain=api.example.com');
     *
     * $domain->value(); // Domain=api.example.com
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic HTTP parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare the parameter name.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP cookie Domain attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter syntax is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the domain is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP cookie Domain attribute.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('Domain')))
            throw new InvalidFieldValueException(
                'Expected an HTTP cookie attribute named "Domain".'
            );

        /** @var non-empty-string $domain */
        $domain = $parameter->content()->value();

        return new self($domain);

    }

    /**
     * ### Creates a Cookie Domain from a URI Host
     *
     * <code>
     * $domain = Domain::fromHost(
     *     new Host('example.com')
     * );
     *
     * $domain->value(); // Domain=example.com
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::value() To retrieve the hostname.
     *
     * @param \FireHub\Foundation\Uri\Component\Host<non-empty-string> $host <p>
     * URI host.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the domain is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Cookie domain attribute.
     */
    public static function fromHost (Host $host):self {

        return new self($host->value());

    }

    /**
     * ### Gets the Foundation URI Host
     *
     * <code>
     * $domain = new Domain('example.com');
     *
     * $domain->host()->value(); // example.com
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::value() To retrieve the hostname.
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidHostException If host has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Foundation\Uri\Component\Host<non-empty-string> Domain hostname.
     */
    public function host ():Host {

        /** @var non-empty-string $domain */
        $domain = $this->content()->value();

        return new Host($domain);

    }

}