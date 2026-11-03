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

namespace FireHub\Adapter\HTTP\Header\ContentDisposition;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentDispositionException;
use FireHub\Runtime;

/**
 * ### Represents a custom content disposition type
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Custom extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the content disposition type.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the HTTP token syntax.
     *
     * @param non-empty-string $type Custom disposition type.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentDispositionException If the type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $type
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\\z/",
                $type
            ),
            fn() => new InvalidContentDispositionException(
                'Invalid content disposition type.'
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->type;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the type to lowercase.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->type);

    }

}