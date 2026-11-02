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

namespace FireHub\Adapter\HTTP\Cookie;

use FireHub\Core\Type\ValueObject;
use FireHub\Foundation\Uri\Component\Host;
use FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieDomainException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP cookie domain
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Domain extends ValueObject {

    /**
     * ### URI host
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\Uri\Component\Host<non-empty-string>
     */
    private Host $host;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the cookie domain.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the cookie domain syntax.
     * @uses \FireHub\Foundation\Uri\Component\Host::value() To get the cookie domain value.
     * @uses \FireHub\Foundation\Uri\Component\Host::normalized() To normalize the cookie domain.
     *
     * @param non-empty-string $host <p>
     * Cookie domain host.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookieDomainException If the domain is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $host) {

        $this->host = new Host($host);

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A(?=.{1,253}\z)[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*\z/',
                $this->host->value()
            ),
            fn() => new InvalidCookieDomainException
        );

    }

    /**
     * ### Gets the cookie domain
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Host<non-empty-string> Cookie domain.
     */
    public function host ():Host {

        return $this->host;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::normalized() To normalize the cookie domain.
     */
    public function value ():string {

        /** @var non-empty-string */
        return $this->host->normalized();

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::normalized() To normalize the cookie domain.
     */
    protected function comparisonValue ():string {

        return $this->host->normalized();

    }

}