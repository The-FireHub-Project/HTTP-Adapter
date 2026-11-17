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
    Cookie\Request as RequestCookies,
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
     * ### Sets Request Cookies
     *
     * Returns a new headers collection with the Cookie header replaced by the serialized request cookie collection.
     *
     * All cookie pairs are serialized into a single Cookie header field, preserving their original order and
     * duplicate names.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Cookie;
     * use FireHub\Adapter\HTTP\Header\Cookie\Request as RequestCookies;
     *
     * $cookies = new RequestCookies(
     *     Cookie::parse('session=abc123'),
     *     Cookie::parse('theme=dark')
     * );
     *
     * $headers = $headers->withCookie($cookies);
     *
     * $headers->getCookie()->value();
     * // session=abc123; theme=dark
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the Cookie header.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Request::value() To serialize the cookie collection.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Request $cookies <p>
     * Request cookie collection.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized Cookie header
     * field value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If a value-object validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a validation exception is not a FireHubException.
     *
     * @return static New headers collection with updated cookies.
     */
    public function withCookie (RequestCookies $cookies):self {

        return $this->with(
            new Name('Cookie'),
            new Value($cookies->value())
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
     * ### Gets Request Cookies
     *
     * Retrieves and parses all Cookie header fields into a single immutable collection of HTTP request cookies.
     *
     * Cookie pairs are preserved in their original order, including cookies with duplicate names.
     *
     * If no Cookie header fields exist, an empty collection is returned.
     *
     * <code>
     * $cookies = $headers->getCookie();
     *
     * $cookies->get(
     *     new \FireHub\Adapter\HTTP\Header\Cookie\Name('session')
     * )?->content()->value();
     * // abc123
     *
     * foreach ($cookies->cookies() as $cookie)
     *     echo $cookie->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Request::parse() To parse each Cookie header field.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Request::cookies() To retrieve the parsed cookie pairs.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a Cookie header contains
     * malformed cookie pairs.
     * @throws \FireHub\Core\Exception\FireHubException If a cookie validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a validation exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie\Request Collection of request cookies.
     */
    public function getCookie ():RequestCookies {

        $cookies = [];

        foreach ($this->get(new Name('Cookie')) as $value) {

            $parsed = RequestCookies::parse($value->value());

            foreach ($parsed->cookies() as $cookie)
                $cookies[] = $cookie;

        }

        return new RequestCookies(...$cookies);

    }

}