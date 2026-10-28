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

namespace FireHub\Adapter\HTTP\Header\Media;

use FireHub\Core\Type\ValueObject;
use FireHub\Runtime;
use FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException;

/**
 * ### Represents a custom HTTP media type
 *
 * Immutable value object representing a valid media type that is not necessarily included in the predefined media
 * type enumeration.
 *
 * Custom media types consist of a type and subtype separated by a forward slash. This value object does not support
 * wildcard media ranges.
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
     * @param TValue $media <p>
     * Custom HTTP media type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $media
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\/[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
                $media
            ),
            fn() => new InvalidMediaTypeException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->media;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the media type to lowercase.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->media);

    }

}