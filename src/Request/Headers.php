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
     * Replaces all existing Accept header fields with the supplied collection of media ranges.
     *
     * The supplied 'Accept' instance is serialized before storage.
     *
     * <code>
     * $headers = Headers::empty()->withAccept(
     *     Accept::parse('text/html, application/json;q=0.8')
     * );
     *
     * $headers->getAccept()?->value();
     * // text/html, application/json; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept::value() To serialize the Accept header.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace existing header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept $accept <p>
     * Accept header containing the accepted media ranges.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated request headers.
     */
    public function withAccept (Accept $accept):self {

        return $this->with(
            new Name('Accept'),
            new Value($accept->value())
        );

    }

    /**
     * ### Gets the HTTP Accept Header
     *
     * Retrieves all Accept header field values and parses each field independently.
     *
     * Parsed media ranges are combined into a single Accept collection while preserving their original order.
     *
     * Returns null when the Accept header is absent.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept'), new Value('text/html'))
     *     ->add(new Name('Accept'), new Value('application/json;q=0.8'));
     *
     * $accept = $headers->getAccept();
     *
     * $accept?->count(); // 2
     * $accept?->value(); // text/html, application/json; q=0.8
     *
     * Headers::empty()->getAccept(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve each field value.
     * @uses \FireHub\Adapter\HTTP\Header\Accept::parse() To parse individual header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Items::all() To retrieve parsed media ranges.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the primary value is empty or an
     * attribute is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a quoted string is unterminated.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Accept|null Parsed Accept header, or null if absent.
     */
    public function getAccept ():?Accept {

        $values = $this->get(new Name('Accept'));

        if ($values === []) return null;

        $items = [];

        foreach ($values as $value) {

            $parsed = Accept::parse($value->value());

            foreach ($parsed->all() as $item)
                $items[] = $item;

        }

        return new Accept(...$items);

    }

    /**
     * ### Sets the HTTP Accept-Encoding Header
     *
     * Replaces all existing Accept-Encoding header fields with the supplied collection of accepted content codings.
     *
     * The supplied Encoding instance is serialized before storage.
     *
     * <code>
     * $headers = Headers::empty()->withAcceptEncoding(
     *     AcceptEncoding::parse('gzip, br;q=0.8')
     * );
     *
     * $headers->getAcceptEncoding()?->value();
     * // gzip, br; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding::value() To serialize the accepted content codings.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace existing header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Encoding $encoding <p>
     * Accept-Encoding header containing the accepted content codings.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated request headers.
     */
    public function withAcceptEncoding (AcceptEncoding $encoding):self {

        return $this->with(
            new Name('Accept-Encoding'),
            new Value($encoding->value())
        );

    }

    /**
     * ### Gets the HTTP Accept-Encoding Header
     *
     * Retrieves all Accept-Encoding header field values and parses each field independently.
     *
     * Parsed content codings are combined into a single Encoding collection while preserving their original order.
     *
     * Returns null when the Accept-Encoding header is absent.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept-Encoding'), new Value('gzip'))
     *     ->add(new Name('Accept-Encoding'), new Value('br;q=0.8'));
     *
     * $encoding = $headers->getAcceptEncoding();
     *
     * $encoding?->count(); // 2
     * $encoding?->value(); // gzip, br; q=0.8
     *
     * Headers::empty()->getAcceptEncoding(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept-Encoding header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve each field value.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding::parse() To parse individual header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Items::all() To retrieve parsed content codings.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the primary value is empty or an
     * attribute is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a quoted string is unterminated.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Accept\Encoding|null Parsed Accept-Encoding header, or null if absent.
     */
    public function getAcceptEncoding ():?AcceptEncoding {

        $values = $this->get(new Name('Accept-Encoding'));

        if ($values === []) return null;

        $items = [];

        foreach ($values as $value) {

            $parsed = AcceptEncoding::parse($value->value());

            foreach ($parsed->all() as $item)
                $items[] = $item;

        }

        return new AcceptEncoding(...$items);

    }

    /**
     * ### Sets the HTTP Accept-Language Header
     *
     * Replaces all existing Accept-Language header fields with the supplied collection of accepted language ranges.
     *
     * The supplied Language instance is serialized before storage.
     *
     * <code>
     * $headers = Headers::empty()->withAcceptLanguage(
     *     AcceptLanguage::parse('hr-HR, en-US;q=0.9, en;q=0.8')
     * );
     *
     * $headers->getAcceptLanguage()?->value();
     * // hr-HR, en-US; q=0.9, en; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language::value() To serialize the accepted language ranges.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace existing header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Language $language <p>
     * Accept-Language header containing the accepted language ranges.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated request headers.
     */
    public function withAcceptLanguage (AcceptLanguage $language):self {

        return $this->with(
            new Name('Accept-Language'),
            new Value($language->value())
        );

    }

    /**
     * ### Gets the HTTP Accept-Language Header
     *
     * Retrieves all Accept-Language header field values and parses each field independently.
     *
     * Parsed language ranges are combined into a single Language collection while preserving their original order.
     *
     * Returns null when the Accept-Language header is absent.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept-Language'), new Value('hr-HR'))
     *     ->add(new Name('Accept-Language'), new Value('en-US;q=0.9'))
     *     ->add(new Name('Accept-Language'), new Value('en;q=0.8'));
     *
     * $language = $headers->getAcceptLanguage();
     *
     * $language?->count(); // 3
     * $language?->value(); // hr-HR, en-US; q=0.9, en; q=0.8
     *
     * // Missing header.
     * Headers::empty()->getAcceptLanguage(); // null
     *
     * // Present but empty header.
     * $headers = Headers::empty()
     *     ->add(new Name('Accept-Language'), new Value(''));
     *
     * $headers->getAcceptLanguage()?->count(); // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept-Language header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve each field value.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language::parse() To parse individual header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Items::all() To retrieve parsed language ranges.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the primary value is empty or an
     * attribute is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a language range or attribute
     * is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Accept\Language|null Parsed Accept-Language header, or null if absent.
     */
    public function getAcceptLanguage ():?AcceptLanguage {

        $values = $this->get(new Name('Accept-Language'));

        if ($values === []) return null;

        $items = [];

        foreach ($values as $value) {

            $parsed = AcceptLanguage::parse($value->value());

            foreach ($parsed->all() as $item)
                $items[] = $item;

        }

        return new AcceptLanguage(...$items);

    }

}