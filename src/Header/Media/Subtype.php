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
 * ### HTTP Media Subtype
 *
 * Represents the subtype component of a MIME media type.
 *
 * A media subtype identifies the specific format of a representation within its primary media type, such as JSON,
 * HTML, XML, or plain.
 *
 * Supports standard, vendor-specific, personal, and custom subtypes, including structured syntax suffixes such as
 * +json and +xml.
 *
 * Media subtype names are case-insensitive. The original casing is preserved for serialization, while comparisons
 * use lowercase values.
 *
 * This class does not support wildcard media ranges.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Media\Subtype;
 *
 * $subtype = new Subtype('json');
 *
 * $subtype->value(); // json
 *
 * $subtype = new Subtype('vnd.firehub+json');
 *
 * $subtype->value(); // vnd.firehub+json
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Subtype extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the media subtype.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the media subtype syntax.
     *
     * @param non-empty-string $subtype <p>
     * MIME media subtype.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media subtype is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $subtype
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                "/\A[!#$%&'+.^_`|~0-9A-Za-z-]+\z/",
                $subtype
            ),
            fn() => new InvalidMediaTypeException
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $subtype = new Subtype('VND.FireHub+JSON');
     *
     * $subtype->value(); // VND.FireHub+JSON
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->subtype;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the media subtype.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->subtype);

    }

}