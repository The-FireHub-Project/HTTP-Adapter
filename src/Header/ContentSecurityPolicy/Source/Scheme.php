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

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Source;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException;
use FireHub\Runtime;

/**
 * ### Represents a CSP scheme source expression
 *
 * Defines a URI scheme permitted by a Content Security Policy source-list directive.
 *
 * Examples include https:, data:, and blob:.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Scheme extends ValueObject implements SourceExpression {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the scheme.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the scheme syntax.
     *
     * @param non-empty-string $scheme <p>
     * URI scheme without the trailing colon.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCSPSourceException If the scheme is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $scheme
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/^[a-zA-Z][a-zA-Z0-9+.-]*$/D',
                    $scheme
                ),
            fn() => new InvalidCSPSourceException
        );

    }

    /**
     * ### Gets the URI scheme
     * @since 1.0.0
     *
     * @return non-empty-string Scheme without the trailing colon.
     */
    public function scheme ():string {

        return $this->scheme;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the scheme to lowercase.
     *
     * @return non-empty-string Serialized CSP scheme source.
     */
    public function value ():string {

        return Runtime\Str\SB\Casing::toLower($this->scheme).':';

    }

}