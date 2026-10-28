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

namespace FireHub\Adapter\HTTP\Header\Language;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidLanguageException;
use FireHub\Runtime;

/**
 * ### Represents a custom HTTP language range
 *
 * Immutable value object representing a language range used in HTTP Accept-Language content negotiation.
 *
 * Supports basic language ranges as defined by RFC 4647.
 *
 * Language ranges are case-insensitive. The original casing is preserved for serialization.
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
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the media type.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the media type syntax.
     *
     * @param TValue $language <p>
     * Custom language range.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidLanguageException If the language range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $language
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/\A[A-Za-z]{1,8}(?:-[A-Za-z0-9]{1,8})*\z/D',
                    $language
                ),
            fn() => new InvalidLanguageException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->language;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the language to lowercase.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->language);

    }

}