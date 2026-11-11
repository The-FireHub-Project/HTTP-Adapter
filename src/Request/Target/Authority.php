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
 *
 * <code>
 * use FireHub\Adapter\HTTP\Request\Target\Authority;
 * use FireHub\Foundation\Uri\Component\Host;
 * use FireHub\Foundation\Uri\Component\Port;
 *
 * $target = new Authority(
 *     new Host('example.com'),
 *     new Port(443)
 * );
 *
 * // example.com:443
 * echo $target->value();
 * </code>
 *
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
     * ### Creates an authority-form target from a URI authority
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     * use FireHub\Foundation\Uri\Component\Authority as UriAuthority;
     * use FireHub\Foundation\Uri\Component\Host;
     * use FireHub\Foundation\Uri\Component\Port;
     *
     * $authority = new UriAuthority(
     *     new Host('example.com'),
     *     new Port(443)
     * );
     *
     * $target = Authority::fromAuthority($authority);
     *
     * // example.com:443
     * echo $target->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Authority::parse() To parse the authority.
     * @uses \FireHub\Foundation\Uri\Component\Authority::userInfo() To validate the user information.
     * @uses \FireHub\Foundation\Uri\Component\Authority::port() To validate the port.
     * @uses \FireHub\Foundation\Uri\Component\Authority::host() To retrieve the host.
     *
     * @param \FireHub\Foundation\Uri\Component\Authority $authority <p>
     * URI authority containing a destination host and port.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the authority is invalid.
     *
     * @return self Authority-form request target.
     */
    public static function fromAuthority (UriAuthority $authority):self {

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
     * ### Parses an authority-form request target
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     *
     * $target = Authority::parse('example.com:443');
     *
     * // example.com:443
     * echo $target->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Target\Authority::fromAuthority() To create an authority-form target from
     * a URI authority.
     * @uses \FireHub\Foundation\Uri\Component\Authority::parse() To parse the authority.
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

        return self::fromAuthority(
            UriAuthority::parse($target)
        );

    }

    /**
     * ### Returns a target with a different host
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     * use FireHub\Foundation\Uri\Component\Host;
     *
     * $target = Authority::parse('example.com:443');
     *
     * $updated = $target->withHost(
     *     new Host('api.example.com')
     * );
     *
     * // api.example.com:443
     * echo $updated->value();
     * </code>
     *
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
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     * use FireHub\Foundation\Uri\Component\Port;
     *
     * $target = Authority::parse('example.com:443');
     *
     * $updated = $target->withPort(
     *     new Port(8443)
     * );
     *
     * // example.com:8443
     * echo $updated->value();
     * </code>
     *
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
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     *
     * $target = Authority::parse('example.com:443');
     *
     * // example.com
     * echo $target->host()->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Host<string> Destination host.
     */
    public function host ():Host {

        return $this->host;

    }

    /**
     * ### Gets the destination port
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     *
     * $target = Authority::parse('example.com:443');
     *
     * // 443
     * echo $target->port()->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Port<int> Destination port.
     */
    public function port ():Port {

        return $this->port;

    }

    /**
     * ### Converts the target into a URI authority
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     *
     * $target = Authority::parse('example.com:443');
     *
     * $authority = $target->authority();
     *
     * // example.com:443
     * echo $authority->value();
     * </code>
     *
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
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Authority;
     *
     * $target = Authority::parse('example.com:443');
     *
     * // example.com:443
     * echo $target->value();
     * </code>
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