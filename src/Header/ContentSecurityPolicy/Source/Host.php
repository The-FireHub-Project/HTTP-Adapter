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
 * ### Represents a CSP host source expression
 *
 * Defines an allowed resource source using a host pattern, optional scheme, optional port, and optional path.
 *
 * Supports wildcard hosts and wildcard ports.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Host extends ValueObject implements SourceExpression {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the host pattern.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the host pattern syntax.
     * @uses \FireHub\Runtime\DataIs::int() To validate the port number.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the path syntax.
     *
     * @param non-empty-string $host <p>
     * Host name, wildcard subdomain pattern, or "*".
     * </p>
     * @param null|non-empty-string $scheme [optional] <p>
     * URI scheme without the trailing colon.
     * </p>
     * @param null|int|string $port [optional] <p>
     * Explicit port number or "*" for any port.
     * </p>
     * @param null|non-empty-string $path [optional] <p>
     * Optional absolute path restriction.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException If the source is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $host,
        private ?string $scheme = null,
        private int|string|null $port = null,
        private ?string $path = null
    ) {

        $this->guard(
            fn() => $this->validHost($host),
            fn() => new InvalidCSPSourceException(
                'Invalid CSP host pattern.'
            )
        );

        $this->guard(
            fn() => $scheme === null
                || Runtime\Str\SB\Regex::match('/^[a-zA-Z][a-zA-Z0-9+.-]*$/D', $scheme),
            fn() => new InvalidCSPSourceException(
                'Invalid CSP source scheme.'
            )
        );

        $this->guard(
            fn() => $port === null
                || $port === '*'
                || (Runtime\DataIs::int($port) && $port >= 1 && $port <= 65535),
            fn() => new InvalidCSPSourceException(
                'Invalid CSP source port.'
            )
        );

        $this->guard(
            fn() => $path === null
                || (
                    Runtime\Str\SB\Search::startsWith('/', $path)
                    && !Runtime\Str\SB\Regex::match('/[\x00-\x20\x7F?#]/', $path)
                ),
            fn() => new InvalidCSPSourceException(
                'Invalid CSP source path.'
            )
        );

        $this->guard(
            fn() => $host !== '*'
                || ($path === null),
            fn() => new InvalidCSPSourceException(
                'A wildcard host cannot define a path restriction.'
            )
        );

    }

    /**
     * ### Checks whether the host is a wildcard
     * @since 1.0.0
     *
     * @return bool True if the host is "*".
     */
    public function isWildcard ():bool {

        return $this->host === '*';

    }

    /**
     * ### Checks whether the source matches subdomains
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check for a subdomain wildcard.
     *
     * @return bool True if the host uses a subdomain wildcard.
     */
    public function includesSubdomains ():bool {

        return Runtime\Str\SB\Search::startsWith('*.', $this->host);

    }

    /**
     * ### Gets the host pattern
     * @since 1.0.0
     *
     * @return non-empty-string Host pattern.
     */
    public function host ():string {

        return $this->host;

    }

    /**
     * ### Gets the source scheme
     * @since 1.0.0
     *
     * @return null|non-empty-string Source scheme.
     */
    public function scheme ():?string {

        return $this->scheme;

    }

    /**
     * ### Gets the source port
     * @since 1.0.0
     *
     * @return null|int|string Source port.
     */
    public function port ():int|string|null {

        return $this->port;

    }

    /**
     * ### Gets the source path
     * @since 1.0.0
     *
     * @return null|non-empty-string Source path.
     */
    public function path ():?string {

        return $this->path;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the source to lowercase.
     *
     * @return non-empty-string Serialized CSP host source.
     */
    public function value ():string {

        $value = '';

        if ($this->scheme !== null)
            $value .= Runtime\Str\SB\Casing::toLower($this->scheme).'://';

        $value .= Runtime\Str\SB\Casing::toLower($this->host);

        if ($this->port !== null)
            $value .= ':'.$this->port;

        if ($this->path !== null)
            $value .= $this->path;

        /** @var non-empty-string */
        return $value;

    }

    /**
     * ### Validates a CSP host pattern
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the wildcard syntax.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the host pattern syntax.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To validate the host length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To remove the wildcard prefix.
     *
     * @param string $host <p>
     * Host pattern.
     * </p>
     *
     * @return bool True if the host pattern is valid.
     */
    private function validHost (string $host):bool {

        if ($host === '*') return true;

        if (Runtime\Str\SB\Search::startsWith('*.', $host))
            $host = Runtime\Str\SB\Access::part($host, 2);

        if ($host === '' || Runtime\Str\SB\Inspection::length($host) > 253) return false;

        return Runtime\Str\SB\Regex::match(
            '/^(?:[a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+$/D',
            $host
        );

    }

}