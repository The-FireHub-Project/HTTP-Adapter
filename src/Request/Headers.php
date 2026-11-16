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

namespace FireHub\Adapter\HTTP\Request;

use FireHub\Adapter\HTTP\Message\Headers as MessageHeaders;
use FireHub\Adapter\HTTP\Header\ {
    Accept\Encoding as AcceptEncoding, Accept\Language as AcceptLanguage,
    Content\Encoding as ContentEncoding, Content\Language as ContentLanguage,
    Accept, Name, Value
};

/**
 * ### Represents immutable HTTP request headers
 *
 * Provides request-specific header operations while delegating header storage and case-insensitive matching to the
 * underlying HTTP message header collection.
 * @since 1.0.0
 */
final readonly class Headers extends MessageHeaders {

    /**
     * ### Sets the HTTP Accept Header
     *
     * Returns a new headers instance with the Accept header replaced by the provided media range preferences.
     *
     * Any existing Accept header fields are removed and replaced with a single serialized Accept header field.
     *
     * <code>
     * $headers = Headers::empty()->withAccept(
     *     Accept::parse('text/html, application/json;q=0.9')
     * );
     *
     * $headers->getAccept()?->count(); // 2
     *
     * $headers = $headers->withAccept(
     *     Accept::parse('image/webp;q=0.8')
     * );
     *
     * $headers->getAccept()?->value(); // image/webp; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the Accept header.
     * @uses \FireHub\Adapter\HTTP\Header\Accept::value() To serialize the Accept preferences.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept $accept <p>
     * Accept media range preferences.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @return static New headers instance with the updated Accept header.
     */
    public function withAccept (Accept $accept):self {

        return $this->with(
            new Name('Accept'),
            new Value($accept->value())
        );

    }

    /**
     * ### Sets the Accept-Encoding Header
     *
     * Replaces all existing Accept-Encoding header fields with the serialized content coding preferences.
     *
     * Preserves an explicitly empty Accept-Encoding value when the provided encoding contains no preferences.
     *
     * <code>
     * $headers = Headers::empty()->withAcceptEncoding(
     *     Encoding::of(Content::GZIP, Content::BR)
     * );
     *
     * $headers->getAcceptEncoding()?->value(); // gzip, br
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding::value() To serialize the content coding preferences.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace existing Accept-Encoding header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Encoding $encoding <p>
     * HTTP Accept-Encoding header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated headers collection.
     */
    public function withAcceptEncoding (AcceptEncoding $encoding):self {

        return $this->with(
            new Name('Accept-Encoding'),
            new Value($encoding->value())
        );

    }

    /**
     * ### Sets the HTTP Accept-Language Header
     *
     * Returns a new headers instance with the Accept-Language header replaced by the provided language preferences.
     *
     * Any existing Accept-Language header fields are removed and replaced with a single serialized header field.
     *
     * <code>
     * $headers = Headers::empty()->withAcceptLanguage(
     *     Language::of(
     *         Range::of('hr-HR'),
     *         Preference::of(
     *             Range::of('en-US'),
     *             Quality::of('0.8')
     *         )
     *     )
     * );
     *
     * $headers->getAcceptLanguage()?->count(); // 2
     *
     * $headers->getAcceptLanguage()?->value();
     * // hr-HR, en-US; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language::value() To serialize language preferences.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the Accept-Language header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Language $language <p>
     * HTTP Accept-Language preferences.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static New headers instance with the updated Accept-Language header.
     */
    public function withAcceptLanguage (AcceptLanguage $language):self {

        return $this->with(
            new Name('Accept-Language'),
            new Value($language->value())
        );

    }

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
     * ### Gets the HTTP Accept Header
     *
     * Returns all media range preferences declared in the Accept header.
     *
     * Multiple Accept header fields are combined while preserving
     * their original order.
     *
     *  * <code>
     * $headers = Headers::empty()->withAccept(
     *     Accept::of(
     *         KnownMediaType::TEXT_HTML,
     *         KnownMediaType::APPLICATION_JSON
     *     )
     * );
     *
     * $accept = $headers->getAccept();
     *
     * $accept?->count(); // 2
     * $accept?->value(); // text/html, application/json
     *
     * Headers::empty()->getAccept(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept header values.
     * @uses \FireHub\Adapter\HTTP\Header\Accept::parse() To parse Accept preferences.
     *
     * @return \FireHub\Adapter\HTTP\Header\Accept|null Parsed Accept header, or null if absent.
     */
    public function getAccept ():?Accept {

        $preferences = [];
        $values = $this->get(new Name('Accept'));

        if ($values === []) return null;

        foreach ($values as $value) {

            $accept = Accept::parse($value->value());

            foreach ($accept->all() as $preference)
                $preferences[] = $preference;

        }

        return new Accept(...$preferences);

    }

    /**
     * ### Gets the Accept-Encoding Header
     *
     * Retrieves and parses all Accept-Encoding header fields into a single collection of content coding preferences.
     *
     * Returns null when the header is absent. An explicitly empty Accept-Encoding header returns an Encoding instance
     * containing no preferences.
     *
     * Multiple Accept-Encoding header fields are combined while preserving the original preference order.
     *
     * <code>
     * $headers = Headers::empty()->withAcceptEncoding(
     *     Encoding::of(Content::GZIP, Content::BR)
     * );
     *
     * $encoding = $headers->getAcceptEncoding();
     *
     * $encoding?->count(); // 2
     * $encoding?->value(); // gzip, br
     *
     * Headers::empty()->getAcceptEncoding(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve all Accept-Encoding header values.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding::parse() To parse each header field value.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding::all() To retrieve parsed content coding preferences.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a content coding is unsupported.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a quality parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Accept\Encoding Parsed Accept-Encoding header, or null if absent.
     */
    public function getAcceptEncoding ():?AcceptEncoding {

        $values = $this->get(new Name('Accept-Encoding'));

        if ($values === []) return null;

        $preferences = [];

        foreach ($values as $value) {

            $encoding = AcceptEncoding::parse($value->value());

            foreach ($encoding->all() as $preference)
                $preferences[] = $preference;

        }

        return new AcceptEncoding(...$preferences);

    }

    /**
     * ### Gets the HTTP Accept-Language Header
     *
     * Retrieves and parses all Accept-Language header fields into a single collection of language preferences.
     *
     * Multiple Accept-Language header fields are combined while preserving their original preference order.
     *
     * Returns null when the header is absent.
     *
     * An explicitly empty Accept-Language header returns a Language instance
     * containing no preferences.
     *
     * <code>
     * $headers = Headers::empty()->withAcceptLanguage(
     *     Language::of(
     *         Range::of('hr-HR'),
     *         Preference::of(
     *             Range::of('en-US'),
     *             Quality::of('0.8')
     *         )
     *     )
     * );
     *
     * $language = $headers->getAcceptLanguage();
     *
     * $language?->count(); // 2
     * $language?->value(); // hr-HR, en-US; q=0.8
     *
     * Headers::empty()->getAcceptLanguage(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept-Language header values.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language::parse() To parse each header field value.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language::all() To retrieve parsed language preferences.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a language range is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a quality parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Accept\Language Parsed Accept-Language header, or null if absent.
     */
    public function getAcceptLanguage ():?AcceptLanguage {

        $values = $this->get(new Name('Accept-Language'));

        if ($values === []) return null;

        $preferences = [];

        foreach ($values as $value) {

            $language = AcceptLanguage::parse($value->value());

            foreach ($language->all() as $preference)
                $preferences[] = $preference;

        }

        return new AcceptLanguage(...$preferences);

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