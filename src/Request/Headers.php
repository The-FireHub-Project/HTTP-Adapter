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
     * ### Gets the HTTP Accept Header
     *
     * Returns all media range preferences declared in the Accept header.
     *
     * Multiple Accept header fields are combined while preserving
     * their original order.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(
     *         new Name('Accept'),
     *         new Value('text/html, application/json;q=0.9')
     *     )
     *     ->add(
     *         new Name('Accept'),
     *         new Value('image/webp;q=0.8')
     *     );
     *
     * $accept = $headers->getAccept();
     *
     * $accept->count(); // 3
     * $accept->value();
     * // text/html, application/json; q=0.9, image/webp; q=0.8
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

}