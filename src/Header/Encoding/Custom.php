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

namespace FireHub\Adapter\HTTP\Header\Encoding;

use FireHub\Core\Type\ValueObject;
use FireHub\Runtime;
use FireHub\Adapter\HTTP\Header\Exception\InvalidEncodingException;

/**
 * ### Represents a custom HTTP content coding
 *
 * Immutable value object representing an HTTP content coding identifier that is not necessarily included in the
 * predefined encoding enumeration.
 *
 * Content-coding identifiers are case-insensitive HTTP tokens. The original casing is preserved for serialization
 * and presentation.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Custom extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the content coding identifier.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the HTTP token syntax.
     *
     * @param TValue $encoding <p>
     * Custom HTTP content coding identifier.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidEncodingException If the content coding
     * identifier is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $encoding
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
                $encoding
            ),
            fn() => new InvalidEncodingException
        );

    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->encoding;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the content coding identifier.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->encoding);

    }

}