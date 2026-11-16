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
    Content\Encoding as ContentEncoding, Content\Language as ContentLanguage,
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

    /**
     * ### Sets the HTTP Content-Language Header
     *
     * Replaces all existing Content-Language header fields with the provided language tags.
     *
     * <code>
     * $headers = Headers::empty()->withContentLanguage(
     *     ContentLanguage::of(
     *         Tag::of('hr-HR'),
     *         Tag::of('en-US')
     *     )
     * );
     *
     * $headers->getContentLanguage()?->value(); // hr-HR, en-US
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::value() To serialize language tags.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the Content-Language header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Language $language <p>
     * Content language tags.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated headers collection.
     */
    public function withContentLanguage (ContentLanguage $language):self {

        return $this->with(
            new Name('Content-Language'),
            new Value($language->value())
        );

    }

    /**
     * ### Gets the HTTP Content-Encoding Header
     *
     * Retrieves and parses all Content-Encoding header fields into a single ordered collection of content codings.
     *
     * Multiple Content-Encoding fields are combined while preserving their original order.
     *
     * Returns null when the header is absent.
     *
     * <code>
     * $headers = Headers::empty()->withContentEncoding(
     *     Encoding::of(
     *         Content::GZIP,
     *         Content::BR
     *     )
     * );
     *
     * $encoding = $headers->getContentEncoding();
     *
     * $encoding?->count(); // 2
     * $encoding?->value(); // gzip, br
     *
     * Headers::empty()->getContentEncoding(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Content-Encoding header values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::parse() To parse each header field.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::all() To retrieve parsed codings.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a content coding is unsupported.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Encoding Parsed Content-Encoding header, or null if absent.
     */
    public function getContentEncoding ():?ContentEncoding {

        $values = $this->get(new Name('Content-Encoding'));

        if ($values === []) return null;

        $codings = [];

        foreach ($values as $value) {

            $encoding = ContentEncoding::parse($value->value());

            foreach ($encoding->all() as $coding)
                $codings[] = $coding;

        }

        return new ContentEncoding(...$codings);

    }

    /**
     * ### Gets the HTTP Content-Language Header
     *
     * Retrieves and parses all Content-Language header fields into a single collection of language tags.
     *
     * Multiple fields are combined while preserving their original order.
     *
     * Returns null when the header is absent.
     *
     * <code>
     * $headers = Headers::empty()->withContentLanguage(
     *     ContentLanguage::of(
     *         Tag::of('hr-HR'),
     *         Tag::of('en-US')
     *     )
     * );
     *
     * $language = $headers->getContentLanguage();
     *
     * $language?->count(); // 2
     * $language?->value(); // hr-HR, en-US
     *
     * Headers::empty()->getContentLanguage(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Content-Language fields.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::parse() To parse individual field values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::all() To retrieve parsed language tags.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Language Parsed Content-Language header, or null if absent.
     */
    public function getContentLanguage ():?ContentLanguage {

        $values = $this->get(new Name('Content-Language'));

        if ($values === []) return null;

        $tags = [];

        foreach ($values as $value) {

            $language = ContentLanguage::parse($value->value());

            foreach ($language->all() as $tag)
                $tags[] = $tag;

        }

        return new ContentLanguage(...$tags);

    }

}