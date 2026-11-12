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
use FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException;
use FireHub\Runtime;

/**
 * ### HTTP Media Type
 *
 * Represents the primary type component of a MIME media type.
 *
 * A media type identifies the general category of a representation, such as application, text, image, audio, video,
 * or a custom type.
 *
 * Media type names are case-insensitive. The original casing is preserved for serialization, while comparisons use
 * lowercase values.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Media\Type;
 *
 * $type = new Type('application');
 *
 * $type->value(); // application
 *
 * $type = new Type('custom');
 *
 * $type->value(); // custom
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Type extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the media type.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the media type syntax.
     *
     * @param non-empty-string $media <p>
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
                "/\A[!#$%&'+.^_`|~0-9A-Za-z-]+\z/",
                $media
            ),
            fn() => new InvalidMediaTypeException
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $type = new Type('application');
     *
     * $type->value(); // application
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->media;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the media type.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->media);

    }

}