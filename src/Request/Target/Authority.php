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

namespace FireHub\Adapter\HTTP\Request\Target;

use FireHub\Adapter\HTTP\Request\Target;
use FireHub\Foundation\Uri\Component\ {
    Authority as UriAuthority,
    Host, Port
};
use FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException;

/**
 * ### Represents an HTTP authority-form request target
 *
 * Contains a host and port identifying the destination of a CONNECT request.
 *
 * User information is not permitted.
 * @since 1.0.0
 */
final readonly class Authority implements Target {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::isEmpty() To validate the host.
     *
     * @param \FireHub\Foundation\Uri\Component\Host<string> $host <p>
     * Destination host.
     * </p>
     * @param \FireHub\Foundation\Uri\Component\Port<int> $port <p>
     * Destination port.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the host is empty.
     *
     * @return void
     */
    public function __construct (
        private Host $host,
        private Port $port
    ) {

        if ($host->isEmpty())
            throw new InvalidTargetException(
                'Authority-form target requires a non-empty host.'
            );

    }

    /**
     * ### Parses an authority-form request target
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Authority::parse() To parse the authority.
     * @uses \FireHub\Foundation\Uri\Component\Authority::userInfo() To validate the user information.
     * @uses \FireHub\Foundation\Uri\Component\Authority::port() To validate the port.
     * @uses \FireHub\Foundation\Uri\Component\Authority::host() To retrieve the host.
     *
     * @param string $target <p>
     * Serialized authority-form request target.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the authority is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the authority or port is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed authority-form target.
     */
    public static function parse (string $target):self {

        $authority = UriAuthority::parse($target);

        if ($authority->userInfo() !== null)
            throw new InvalidTargetException(
                'Authority-form target cannot contain user information.'
            );

        $port = $authority->port();

        if ($port === null)
            throw new InvalidTargetException(
                'Authority-form target requires an explicit port.'
            );

        return new self(
            $authority->host(),
            $port
        );

    }

    /**
     * ### Returns a target with a different host
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Uri\Component\Host<string> $host <p>
     * New destination host.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the host is empty.
     *
     * @return self New authority-form target.
     */
    public function withHost (Host $host):self {

        return new self(
            $host,
            $this->port
        );

    }

    /**
     * ### Returns a target with a different port
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Uri\Component\Port<int> $port <p>
     * New destination port.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the host is empty.
     *
     * @return self New authority-form target.
     */
    public function withPort (Port $port):self {

        return new self(
            $this->host,
            $port
        );

    }

    /**
     * ### Gets the destination host
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Host<string> Destination host.
     */
    public function host ():Host {

        return $this->host;

    }

    /**
     * ### Gets the destination port
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Port<int> Destination port.
     */
    public function port ():Port {

        return $this->port;

    }

    /**
     * ### Converts the target into a URI authority
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Authority URI authority.
     */
    public function authority ():UriAuthority {

        return new UriAuthority(
            $this->host,
            $this->port
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Target\Authority::authority() To get the URI authority.
     */
    public function value ():string {

        /** @var non-empty-string */
        return $this->authority()->value();

    }

}