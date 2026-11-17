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

namespace FireHub\Adapter\HTTP\Header\Cookie\Response;

use FireHub\Core\Type\ValueObject;
use FireHub\Foundation\Uri\Component\Host;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie Domain Attribute
 *
 * Represents the Domain attribute of an HTTP Set-Cookie header.
 *
 * Cookie domains are restricted to DNS hostnames.
 *
 * Domain names are compared case-insensitively and serialized in lowercase.
 *
 * The leading dot is not included in the canonical representation.
 *
 * <code>
 * $domain = new Domain('Example.COM');
 *
 * $domain->value();
 * // example.com
 *
 * $domain->headerValue();
 * // Domain=example.com
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Domain extends ValueObject {

    /**
     * ### Domain Host
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\Uri\Component\Host<non-empty-string>
     */
    private Host $host;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the domain.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate DNS hostname syntax.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the hostname.
     *
     * @param non-empty-string $domain <p>
     * DNS hostname.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the domain is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidHostException If the URI host is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $domain) {

        /** @var non-empty-lowercase-string $normalized */
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

        $this->host = new Host($normalized);

    }

    /**
     * ### Creates a Cookie Domain from a URI Host
     *
     * Converts a Foundation URI host into a cookie domain.
     *
     * <code>
     * $host = new Host('example.com');
     *
     * $domain = Domain::fromHost($host);
     *
     * $domain->value();
     * // example.com
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::value() To retrieve the URI host.
     *
     * @param \FireHub\Foundation\Uri\Component\Host<non-empty-string> $host <p>
     * Foundation URI host.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the host cannot be represented as
     * a cookie domain.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Cookie domain.
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
     * $domain->host()->value();
     * // example.com
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Host<non-empty-string> Foundation URI host.
     */
    public function host ():Host {

        return $this->host;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $domain = new Domain('Example.COM');
     *
     * $domain->value();
     * // example.com
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->host->value();

    }

    /**
     * ### Gets the Serialized Domain Attribute
     *
     * <code>
     * $domain = new Domain('example.com');
     *
     * $domain->headerValue();
     * // Domain=example.com
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized Domain attribute.
     */
    public function headerValue ():string {

        return 'Domain='.$this->value();

    }

}