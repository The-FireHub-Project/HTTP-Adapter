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

namespace FireHub\Adapter\HTTP\Response;

use FireHub\Adapter\HTTP\Message\Headers as MessageHeaders;
use FireHub\Adapter\HTTP\Header\ {
    Content\Encoding as ContentEncoding,
    Name, Value
};

/**
 * ### Represents immutable HTTP response headers
 *
 * Provides response-specific header operations while delegating header storage and case-insensitive matching to the
 * underlying HTTP message header collection.
 *
 * Every modification produces a new response header collection.
 * @since 1.0.0
 */
final readonly class Headers extends MessageHeaders {

    /**
     * ### Sets the HTTP Content-Encoding Header
     *
     * Replaces the Content-Encoding header with the provided content codings.
     *
     * <code>
     * $headers = Headers::empty()->withContentEncoding(
     *     Encoding::of(Content::GZIP)
     * );
     *
     * $headers->getContentEncoding()?->value(); // gzip
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::value() To serialize the content codings.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the Content-Encoding header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Encoding $encoding <p>
     * Content codings applied to the representation.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static New headers instance with the updated Content-Encoding header.
     */
    public function withContentEncoding (ContentEncoding $encoding):self {

        return $this->with(
            new Name('Content-Encoding'),
            new Value($encoding->value())
        );

    }

}