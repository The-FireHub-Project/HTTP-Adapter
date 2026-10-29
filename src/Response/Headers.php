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
    CacheControl\Custom, CacheControl\ResponseDirective,
    Content\Length,
    Encoding\Custom as EncodingCustom, Encoding\Type as EncodingType,
    Language\Custom as LanguageCustom, Language\Type as LanguageType,
    Media\ContentType, Media\Custom as MediaCustom, Media\Parameter as MediaParameter,
    Media\Type as MediaType,
    Name, Value
};
use FireHub\Runtime;

/**
 * ### Represents immutable HTTP response headers
 *
 * Provides response-specific header operations while delegating header storage and case-insensitive matching to the
 * underlying HTTP message header collection.
 *
 * Every modification produces a new response header collection.
 * @since 1.0.0
 */
final readonly class Headers {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Message\Headers $headers <p>
     * Underlying HTTP message headers.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private MessageHeaders $headers
    ) {}

    /**
     * ### Creates an empty response header collection
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::empty() To create an empty HTTP message header collection.
     *
     * @return self Empty response header collection.
     */
    public static function empty ():self {

        return new self(MessageHeaders::empty());

    }

    /**
     * ### Sets the Cache-Control response header
     *
     * Specifies caching requirements for the HTTP response.
     *
     * Replaces any previously configured Cache-Control header.
     *
     * <code>
     * $headers = Headers::empty()->cacheControl(
     *     ResponseDirective::public(),
     *     ResponseDirective::maxAge(3600),
     *     ResponseDirective::immutable()
     * );
     *
     * // Cache-Control: public, max-age=3600, immutable
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective::value() To serialize cache directives.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join cache directives.
     *
     * @param \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective|\FireHub\Adapter\HTTP\Header\CacheControl\Custom $directive <p>
     * First cache directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\CacheControl\ResponseDirective|\FireHub\Adapter\HTTP\Header\CacheControl\Custom ...$additional <p>
     * Additional cache directives.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function cacheControl (ResponseDirective|Custom $directive, ResponseDirective|Custom ...$additional):self {

        $values = [];

        foreach ([$directive, ...$additional] as $item)
            $values[] = $item->value();

        return $this->replace(
            'Cache-Control',
            new Value(
                Runtime\Str\SB\Delimiter::implode($values, ', ')
            )
        );

    }

    /**
     * ### Sets the Content-Type response header
     *
     * Specifies the media type of the HTTP response representation.
     *
     * Replaces any previously configured Content-Type header.
     *
     * <code>
     * $headers = Headers::empty()->contentType(
     *     MediaType::APPLICATION_JSON,
     *     new MediaParameter('charset', 'utf-8')
     * );
     *
     * // Content-Type: application/json; charset=utf-8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\ContentType::value() To serialize the content type.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string> $media <p>
     * Concrete media type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string> ...$parameters <p>
     * Optional media type parameters.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function contentType (MediaType|MediaCustom $media, MediaParameter ...$parameters):self {

        return $this->replace(
            'Content-Type',
            new Value(
                new ContentType($media, ...$parameters)->value()
            )
        );

    }

    /**
     * ### Sets the Content-Encoding header
     *
     * Specifies the content codings applied to the representation.
     *
     * <code>
     * $headers = Headers::empty()->contentEncoding(
     *     EncodingType::GZIP
     * );
     *
     * // Content-Encoding: gzip
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Encoding\Type|\FireHub\Adapter\HTTP\Header\Encoding\Custom<non-empty-string> $encoding <p>
     * First content coding.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Encoding\Type|\FireHub\Adapter\HTTP\Header\Encoding\Custom<non-empty-string> ...$additional <p>
     * Additional content codings.
     * </p>
     *
     * @return self Updated header collection.
     */
    public function contentEncoding (EncodingType|EncodingCustom $encoding, EncodingType|EncodingCustom ...$additional):self {

        $values = [
            $encoding instanceof EncodingType
                ? $encoding->value
                : $encoding->value()
        ];

        foreach ($additional as $item)
            $values[] = $item instanceof EncodingType
                ? $item->value
                : $item->value();

        return $this->replace(
            'Content-Encoding',
            new Value(
                Runtime\Str\SB\Delimiter::implode($values, ', ')
            )
        );

    }

    /**
     * ### Sets the Content-Language header
     *
     * Specifies the natural languages of the intended audience for the representation.
     *
     * <code>
     * $headers = Headers::empty()->contentLanguage(
     *     LanguageType::EN_US
     * );
     *
     * // Content-Language: en-US
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join content languages.
     *
     * @param \FireHub\Adapter\HTTP\Header\Language\Type|\FireHub\Adapter\HTTP\Header\Language\Custom<non-empty-string> $language <p>
     * First content language.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Language\Type|\FireHub\Adapter\HTTP\Header\Language\Custom<non-empty-string> ...$additional <p>
     * Additional content languages.
     * </p>
     *
     * @return self Updated header collection.
     */
    public function contentLanguage (LanguageType|LanguageCustom $language, LanguageType|LanguageCustom ...$additional):self {

        $values = [
            $language instanceof LanguageType
                ? $language->value
                : $language->value()
        ];

        foreach ($additional as $item)
            $values[] = $item instanceof LanguageType
                ? $item->value
                : $item->value();

        return $this->replace(
            'Content-Language',
            new Value(Runtime\Str\SB\Delimiter::implode($values, ', '))
        );

    }

    /**
     * ### Sets the Content-Length header
     *
     * Specifies the size of the message content in octets.
     *
     * <code>
     * $headers = Headers::empty()->contentLength(
     *     new Length(1024)
     * );
     *
     * // Content-Length: 1024
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Length::value() To serialize the content length.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Length $length <p>
     * Content length in octets.
     * </p>
     *
     * @return self Updated header collection.
     */
    public function contentLength (Length $length):self {

        return $this->replace(
            'Content-Length',
            new Value($length->value())
        );

    }

    /**
     * ### Adds a custom HTTP response header
     *
     * Preserves existing headers with the same name.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->custom(new Name('X-Request-ID'), new Value('123'));
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::add() To append a header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Header name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> $value <p>
     * Header value.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function custom (Name $name, Value $value):self {

        return new self(
            $this->headers->add($name, $value)
        );

    }

    /**
     * ### Gets all HTTP response header fields
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::all() To retrieve all header fields.
     *
     * @return list<array{
     *     name: \FireHub\Adapter\HTTP\Header\Name<non-empty-string>,
     *     value: \FireHub\Adapter\HTTP\Header\Value<string>
     * }> Header fields.
     */
    public function all ():array {

        return $this->headers->all();

    }

    /**
     * ### Replaces an HTTP response header
     *
     * Replaces all occurrences of the specified header with a new value. If the header does not exist, it is appended.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace existing header fields.
     *
     * @param non-empty-string $name <p>
     * Header field name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> $value <p>
     * Header field value.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> ...$additional <p>
     * Additional header values.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated response header collection.
     */
    private function replace (string $name, Value $value, Value ...$additional):self {

        return new self(
            $this->headers->with(new Name($name), $value, ...$additional)
        );

    }

}