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
use FireHub\Foundation\ {
    Uri\KnownScheme, Uri
};
use FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP absolute-form request target
 *
 * Contains an absolute HTTP or HTTPS URI without a fragment.
 *
 * Absolute-form is primarily used when sending requests to proxies.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Request\Target\Absolute;
 * use FireHub\Foundation\Uri;
 *
 * $target = new Absolute(
 *     Uri::parse('https://example.com/users?page=1')
 * );
 *
 * // https://example.com/users?page=1
 * echo $target->value();
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Absolute implements Target {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\KnownScheme::HTTP To validate the URI scheme.
     * @uses \FireHub\Foundation\Uri\KnownScheme::HTTPS To validate the URI scheme.
     * @uses \FireHub\Foundation\Uri::fragment() To validate the URI fragment.
     * @uses \FireHub\Foundation\Uri::authority() To validate the URI authority.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower To validate the URI scheme.
     *
     * @param \FireHub\Foundation\Uri $uri <p>
     * Absolute HTTP or HTTPS URI.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the URI is not a valid HTTP
     * absolute-form target.
     *
     * @return void
     */
    public function __construct (
        private Uri $uri
    ) {

        $scheme = $uri->scheme();

        if ($scheme === null)
            throw new InvalidTargetException(
                'Absolute-form target requires a URI scheme.'
            );

        $scheme_value = Runtime\Str\SB\Casing::toLower($scheme->value());

        if (
            $scheme_value !== KnownScheme::HTTP->value
            && $scheme_value !== KnownScheme::HTTPS->value
        ) throw new InvalidTargetException(
            'Absolute-form target supports only HTTP and HTTPS schemes.'
        );

        if ($uri->authority() === null)
            throw new InvalidTargetException(
                'Absolute-form target requires an authority.'
            );

        if ($uri->authority()->host()->isEmpty())
            throw new InvalidTargetException(
                'Absolute-form target requires a non-empty host.'
            );

        if ($uri->authority()->userInfo() !== null)
            throw new InvalidTargetException(
                'Absolute-form target cannot contain user information.'
            );

        if ($uri->fragment() !== null)
            throw new InvalidTargetException(
                'Absolute-form target cannot contain a fragment.'
            );

    }

    /**
     * ### Parses an absolute-form request target
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Absolute;
     *
     * $target = Absolute::parse(
     *     'https://example.com/users?page=1'
     * );
     *
     * // https://example.com/users?page=1
     * echo $target->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::parse() To parse the URI.
     *
     * @param string $target <p>
     * Serialized absolute-form request target.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the URI is not a valid HTTP
     * absolute-form target.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidFragmentException If fragment has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed absolute-form target.
     */
    public static function parse (string $target):self {

        return new self(
            Uri::parse($target)
        );

    }

    /**
     * ### Gets the absolute URI
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Absolute;
     *
     * $target = Absolute::parse(
     *     'https://example.com/users?page=1'
     * );
     *
     * $uri = $target->uri();
     *
     * // https://example.com/users?page=1
     * echo $uri->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri Absolute URI.
     */
    public function uri ():Uri {

        return $this->uri;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Target\Absolute;
     *
     * $target = Absolute::parse(
     *     'https://example.com/users?page=1'
     * );
     *
     * // https://example.com/users?page=1
     * echo $target->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::value() To get the URI value.
     */
    public function value ():string {

        /** @var non-empty-string */
        return $this->uri->value();

    }

}