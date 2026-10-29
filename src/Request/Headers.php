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
    Authorization\Credentials,
    CacheControl\Custom, CacheControl\RequestDirective,
    Encoding\Preference as EncodingPreference,
    Language\Preference as LanguagePreference,
    Media\ContentType, Media\Custom as MediaCustom, Media\Parameter as MediaParameter,
    Media\Preference as MediaPreference, Media\Type as MediaType,
    UserAgent\Product as UserAgentProduct, UserAgent\Comment as UserAgentComment,
    Name, Value
};
use FireHub\Runtime;

/**
 * ### Represents immutable HTTP request headers
 *
 * Provides request-specific header operations while delegating header storage and case-insensitive matching to the
 * underlying HTTP message header collection.
 *
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
     * ### Creates an empty request header collection
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::empty() Creates an empty HTTP message header collection.
     *
     * @return self Empty request header collection.
     */
    public static function empty ():self {

        return new self(MessageHeaders::empty());

    }

    /**
     * ### Sets the Authorization request header
     *
     * Specifies authentication credentials used to authenticate the HTTP request with the target server.
     *
     * Replaces any previously configured Authorization header.
     *
     * <code>
     * $headers = Headers::empty()->authorization(
     *     new Bearer('my-access-token')
     * );
     *
     * // Authorization: Bearer my-access-token
     *
     * $headers = Headers::empty()->authorization(
     *     new Basic('username', 'password')
     * );
     *
     * // Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ=
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Authorization\Credentials::value() To serialize authentication credentials.
     * @uses \FireHub\Adapter\HTTP\Request\Headers::replace() To replace the Authorization header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Authorization\Credentials $credentials <p>
     * Authentication credentials.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function authorization (Credentials $credentials):self {

        return $this->replace(
            'Authorization',
            new Value($credentials->value())
        );

    }

    /**
     * ### Sets the Accept request header
     *
     * Specifies the media types accepted by the HTTP client.
     *
     * <code>
     * $headers = Headers::empty()->accept(
     *     new Preference(Type::APPLICATION_JSON),
     *     new Preference(Type::TEXT_HTML, new Quality(0.9)),
     *     new Preference(Range::ANY, new Quality(0.5))
     * );
     *
     * // Accept: application/json, text/html;q=0.9, * / *;q=0.5
     * </code>
         *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::replace() Replaces all existing occurrences of the specified HTTP
     * message header with a new value.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Preference::value() To serialize each media preference.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join media preferences into a single string.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Preference<non-empty-string> $preference <p>
     * First accepted media preference.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Media\Preference<non-empty-string> ...$additional [optional] <p>
     * Additional accepted media preferences.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function accept (MediaPreference $preference, MediaPreference ...$additional):self {

        $values = [];

        foreach ([$preference, ...$additional] as $item)
            $values[] = $item->value();

        return $this->replace(
            'Accept',
            new Value(Runtime\Str\SB\Delimiter::implode($values, ', '))
        );

    }

    /**
     * ### Sets the AcceptEncoding request header
     *
     * Specifies the media types accepted by the HTTP client.
     *
     * <code>
     * $headers = Headers::empty()->acceptEncoding(
     *     new Preference(Type::BR),
     *     new Preference(Type::GZIP, new Quality(0.9)),
     *     new Preference(new Custom('x-firehub-compression'), new Quality(0.5)),
     *     new Preference(Range::ANY, new Quality(0.1))
     * );
     *
     * // Accept-Encoding: br, gzip;q=0.9, x-firehub-compression;q=0.5, *;q=0.1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::replace() Replaces all existing occurrences of the specified HTTP
     * message header with a new value.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Preference::value() To serialize each encoding preference.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join encoding preferences into a single string.
     *
     * @param \FireHub\Adapter\HTTP\Header\Encoding\Preference<non-empty-string> $preference <p>
     * First accepted encoding preference.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Encoding\Preference<non-empty-string> ...$additional [optional] <p>
     * Additional accepted encoding preferences.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function acceptEncoding (EncodingPreference $preference, EncodingPreference ...$additional):self {

        $values = [];

        foreach ([$preference, ...$additional] as $item)
            $values[] = $item->value();

        return $this->replace(
            'Accept-Encoding',
            new Value(Runtime\Str\SB\Delimiter::implode($values, ', '))
        );

    }

    /**
     * ### Sets the Accept-Language request header
     *
     * Specifies the natural languages preferred by the HTTP client.
     *
     * Each language preference may include a quality factor indicating its relative priority. Calling this method
     * replaces any previously configured Accept-Language header.
     *
     * <code>
     * $headers = Headers::empty()->acceptLanguage(
     *     new LanguagePreference(LanguageType::HR_HR),
     *     new LanguagePreference(LanguageType::EN_US, new Quality(0.9)),
     *     new LanguagePreference(LanguageRange::ANY, new Quality(0.1))
     * );
     *
     * // Accept-Language: hr-HR, en-US;q=0.9, *;q=0.1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Language\Preference::value() To serialize each language preference.
     * @uses \FireHub\Adapter\HTTP\Request\Headers::replace() To replace the Accept-Language header.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join language preferences into a single string.
     *
     * @param \FireHub\Adapter\HTTP\Header\Language\Preference<non-empty-string> $preference <p>
     * First accepted language preference.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Language\Preference<non-empty-string> ...$additional [optional] <p>
     * Additional accepted language preferences.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function acceptLanguage (LanguagePreference $preference, LanguagePreference ...$additional):self {

        $values = [];

        foreach ([$preference, ...$additional] as $item)
            $values[] = $item->value();

        return $this->replace(
            'Accept-Language',
            new Value(Runtime\Str\SB\Delimiter::implode($values, ', '))
        );

    }

    /**
     * ### Sets the User-Agent request header
     *
     * Identifies the software originating the HTTP request.
     *
     * <code>
     * $headers = Headers::empty()->userAgent(
     *     new UserAgentProduct('FireHub', '1.0.0'),
     *     new UserAgentComment('HTTP Adapter; PHP 8.5'),
     *     new UserAgentProduct('MyClient', '2.0')
     * );
     *
     * // User-Agent: FireHub/1.0.0 (HTTP Adapter; PHP 8.5) MyClient/2.0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Product::value() To serialize the product.
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Comment::value() To serialize the comment.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join product and comment values into a single string.
     *
     * @param \FireHub\Adapter\HTTP\Header\UserAgent\Product $product <p>
     * Primary product.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\UserAgent\Product|\FireHub\Adapter\HTTP\Header\UserAgent\Comment ...$additional <p>
     * Additional products or comments.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function userAgent (UserAgentProduct $product, UserAgentProduct|UserAgentComment ...$additional):self {

        $values = [];

        foreach ([$product, ...$additional] as $item)
            $values[] = $item->value();

        return $this->replace(
            'User-Agent',
            new Value(
                Runtime\Str\SB\Delimiter::implode($values, ' ')
            )
        );

    }

    /**
     * ### Sets the Cache-Control request header
     *
     * Specifies caching requirements for the HTTP request.
     *
     * <code>
     * $headers = Headers::empty()->cacheControl(
     *     RequestDirective::noCache(),
     *     RequestDirective::noStore(),
     *     RequestDirective::maxAge(3600),
     *     new Custom('my-extension', 'enabled')
     * );
     *
     * // Cache-Control: no-cache, no-store, max-age=3600
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective::value() To serialize each cache directive.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join cache directives into a single string.
     *
     * @param \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective|\FireHub\Adapter\HTTP\Header\CacheControl\Custom $directive <p>
     * First cache directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective|\FireHub\Adapter\HTTP\Header\CacheControl\Custom ...$additional <p>
     * Additional cache directives.
     * </p>
     *
     * @return self Updated request headers.
     */
    public function cacheControl (RequestDirective|Custom $directive, RequestDirective|Custom ...$additional):self {

        $values = [];

        foreach ([$directive, ...$additional] as $item)
            $values[] = $item->value();

        return $this->replace(
            'Cache-Control',
            new Value(
                \FireHub\Runtime\Str\SB\Delimiter::implode($values, ', ')
            )
        );

    }

    /**
     * ### Sets the Content-Type request header
     *
     * Specifies the media type of the HTTP request body, including optional media type parameters such as charset or
     * boundary.
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
     * @uses \FireHub\Adapter\HTTP\Request\Headers::replace() To replace the Content-Type header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string> $media <p>
     * Concrete media type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string> ...$parameters <p>
     * Optional media type parameters.
     * </p>
     *
     * @return self Updated request headers.
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
     * ### Adds a custom HTTP request header
     *
     * <code>
     * $headers = Headers::empty()
     *     ->custom(new Name('X-Request-ID'), new Value('123'))
     *     ->custom(new Name('X-Request-ID'), new Value('456'));
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::add() Adds a custom HTTP message header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * HTTP header name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> $value <p>
     * Header value.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function custom (Name $name, Value $value):self {

        return new self(
            $this->headers->add($name, $value)
        );

    }

    /**
     * ### Gets all HTTP request header fields
     *
     * <code>
     * $headers = Headers::empty()
     *     ->custom(new Name('Accept'), new Value('application/json'))
     *     ->custom(new Name('X-Request-ID'), new Value('123'));
     *
     * foreach ($headers->all() as $header) {
     *     echo $header['name']->value();
     *     echo $header['value']->value();
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::all() Gets all HTTP message header fields.
     *
     * @return list<array{
     *     name: \FireHub\Adapter\HTTP\Header\Name<non-empty-string>,
     *     value: \FireHub\Adapter\HTTP\Header\Value<string>
     * }>
     */
    public function all ():array {

        return $this->headers->all();

    }

    /**
     * ### Replaces an HTTP request header
     *
     * Replaces all existing occurrences of the specified HTTP header with a new value. If the header does not exist,
     * it is appended to the collection.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() Replaces all existing occurrences of the specified HTTP
     * message header with a new value.
     *
     * @param non-empty-string $name <p>
     * HTTP header field name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> $value <p>
     * HTTP header field value.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> ...$additional <p>
     * Additional HTTP header values.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated request header collection.
     */
    private function replace (string $name, Value $value, Value ...$additional):self {

        return new self(
            $this->headers->with(new Name($name), $value, ...$additional)
        );

    }

}