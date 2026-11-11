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
    Authorization\Credentials, Authorization\Parser as AuthorizationParser,
    CacheControl\Custom, CacheControl\RequestDirective, CacheControl\RequestParser as CacheControlParser,
    Connection\Type as ConnectionType, Connection\Custom as ConnectionCustom, Connection\Parser as ConnectionParser,
    Content\Length,
    Cookie\Request as Cookie,
    Encoding\Custom as EncodingCustom, Encoding\Preference as EncodingPreference, Encoding\Type as EncodingType,
    EntityTag\Tag as EntityTag, EntityTag\Range as EntityTagRange,
    Language\Custom as LanguageCustom, Language\Type as LanguageType, Language\Preference as LanguagePreference,
    Listing\Parser as ListingParser,
    Media\ContentType, Media\Custom as MediaCustom, Media\Parameter as MediaParameter,
    Media\Preference as MediaPreference, Media\Type as MediaType,
    Range\ByteRange, Range\SuffixRange, Range\Unit,
    Upgrade\Protocol as UpgradeProtocol, Upgrade\Parser as UpgradeParser,
    UserAgent\Product as UserAgentProduct, UserAgent\Comment as UserAgentComment, UserAgent\Parser as UserAgentParser,
    Date, Name, Value,
};
use FireHub\Adapter\HTTP\Cookie\Request as RequestCookie;
use FireHub\Foundation\ {
    Uri\Component\Authority, Uri\Component\Path, Uri\KnownScheme, Uri
};
use FireHub\Adapter\HTTP\Header\Exception\ {
    InvalidContentLengthException, InvalidCredentialsFormatException, InvalidEntityTagException,
    InvalidHostException, InvalidMediaTypeException, InvalidOriginException, InvalidParameterValueException
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
     * ### Sets the Connection request header
     *
     * Specifies HTTP/1.x connection options.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Request\Headers;
     * use FireHub\Adapter\HTTP\Header\Connection\Type;
     * use FireHub\Adapter\HTTP\Header\Connection\Custom;
     *
     * $headers = Headers::empty()->connection(
     *     Type::KEEP_ALIVE,
     *     Type::UPGRADE,
     *     new Custom('X-Custom-Connection')
     * );
     *
     * // Connection: keep-alive, upgrade, X-Custom-Connection
     *
     * $headers = Headers::empty()->connection(Type::CLOSE);
     *
     * // Connection: close
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Connection\Type::value() To get a standard connection option.
     * @uses \FireHub\Adapter\HTTP\Header\Connection\Custom::value() To get a custom connection option.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join connection options.
     *
     * @param \FireHub\Adapter\HTTP\Header\Connection\Type|\FireHub\Adapter\HTTP\Header\Connection\Custom $option <p>
     * First connection option.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Connection\Type|\FireHub\Adapter\HTTP\Header\Connection\Custom ...$additional <p>
     * Additional connection options.
     * </p>
     *
     * @return self Updated request headers.
     */
    public function connection (ConnectionType|ConnectionCustom $option, ConnectionType|ConnectionCustom ...$additional):self {

        $options = [];

        foreach ([$option, ...$additional] as $item)
            $options[] = $item instanceof ConnectionType
                ? $item->value
                : $item->value();

        return $this->replace(
            'Connection',
            new Value(
                Runtime\Str\SB\Delimiter::implode($options, ', ')
            )
        );

    }

    /**
     * ### Sets the Upgrade request header
     *
     * Specifies protocols supported for an HTTP/1.1 protocol upgrade.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Upgrade\Type;
     * use FireHub\Adapter\HTTP\Header\Upgrade\Protocol;
     *
     * $headers = Headers::empty()->upgrade(
     *     new Protocol(Type::WEBSOCKET),
     *     new Protocol(Type::H2C)
     * );
     *
     * // Upgrade: websocket, h2c
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Upgrade\Protocol::value() To get a protocol upgrade option.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join protocol upgrade options.
     *
     * @param \FireHub\Adapter\HTTP\Header\Upgrade\Protocol $protocol <p>
     * First upgrade protocol.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Upgrade\Protocol ...$additional <p>
     * Additional protocols.
     * </p>
     *
     * @return self Updated request headers.
     */
    public function upgrade (UpgradeProtocol $protocol, UpgradeProtocol ...$additional):self {

        $protocols = [];

        foreach ([$protocol, ...$additional] as $item)
            $protocols[] = $item->value();

        return $this->replace(
            'Upgrade',
            new Value(
                Runtime\Str\SB\Delimiter::implode(
                    $protocols,
                    ', '
                )
            )
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
     * // CCache-Control: no-cache, no-store, max-age=3600, my-extension=enabled
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
     * ### Sets the Content-Location request header
     *
     * Identifies a URI corresponding to the representation enclosed in the HTTP request message.
     *
     * Supports both absolute and relative URIs.
     *
     * <code>
     * $headers = Headers::empty()->contentLocation(
     *     Uri::parse('/documents/report.pdf')
     * );
     *
     * // Content-Location: /documents/report.pdf
     *
     * $headers = Headers::empty()->contentLocation(
     *     Uri::parse('https://example.com/documents/report.pdf')
     * );
     *
     * // Content-Location: https://example.com/documents/report.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::value() To serialize the URI.
     * @uses \FireHub\Adapter\HTTP\Request\Headers::replace() To replace the Content-Location header.
     *
     * @param \FireHub\Foundation\Uri $uri <p>
     * URI identifying the enclosed representation.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function contentLocation (Uri $uri):self {

        return $this->replace(
            'Content-Location',
            new Value($uri->value())
        );

    }

    /**
     * ### Sets the Range request header
     *
     * Requests one or more byte ranges from the target resource.
     *
     * <code>
     * $headers = Headers::empty()->range(
     *     new ByteRange(0, 1023)
     * );
     *
     * // Range: bytes=0-1023
     *
     * $headers = Headers::empty()->range(
     *     new ByteRange(0, 499),
     *     new ByteRange(1000, 1499)
     * );
     *
     * // Range: bytes=0-499,1000-1499
     *
     * $headers = Headers::empty()->range(
     *     new SuffixRange(500)
     * );
     *
     * // Range: bytes=-500
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join byte ranges.
     *
     * @param \FireHub\Adapter\HTTP\Header\Range\ByteRange|\FireHub\Adapter\HTTP\Header\Range\SuffixRange $range <p>
     * First byte range.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Range\ByteRange|\FireHub\Adapter\HTTP\Header\Range\SuffixRange ...$additional <p>
     * Additional byte ranges.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function range (ByteRange|SuffixRange $range, ByteRange|SuffixRange ...$additional):self {

        $values = [$range->value()];

        foreach ($additional as $item)
            $values[] = $item->value();

        return $this->replace(
            'Range',
            new Value(
                Unit::BYTES->value.'='
                .Runtime\Str\SB\Delimiter::implode($values, ',')
            )
        );

    }

    /**
     * ### Sets the If-Match request header
     *
     * Makes the request conditional on the current entity tag of the target resource.
     *
     * Uses strong entity tag comparison when the server evaluates the condition.
     *
     * <code>
     * $headers = Headers::empty()->ifMatch(
     *     new EntityTag('version-123')
     * );
     *
     * // If-Match: "version-123"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join entity tags.
     *
     * @param \FireHub\Adapter\HTTP\Header\EntityTag\Tag|\FireHub\Adapter\HTTP\Header\EntityTag\Range $tag <p>
     * First entity tag or wildcard.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\EntityTag\Tag ...$additional <p>
     * Additional entity tags.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidEntityTagException If a wildcard is combined with entity tags.
     *
     * @return self Updated request header collection.
     */
    public function ifMatch (EntityTag|EntityTagRange $tag, EntityTag ...$additional):self {

        if ($tag instanceof EntityTagRange) {

            if ($additional !== [])
                throw new InvalidEntityTagException(
                    'An entity tag wildcard cannot be combined with additional entity tags.'
                );

            return $this->replace(
                'If-Match',
                new Value($tag->value)
            );

        }

        $values = [$tag->value()];

        foreach ($additional as $item)
            $values[] = $item->value();

        return $this->replace(
            'If-Match',
            new Value(
                Runtime\Str\SB\Delimiter::implode($values, ', ')
            )
        );

    }

    /**
     * ### Sets the If-None-Match request header
     *
     * Makes the request conditional on the current entity tag of the target resource.
     *
     * Uses weak entity tag comparison when the server evaluates the condition.
     *
     * <code>
     * $headers = Headers::empty()->ifNoneMatch(
     *     new EntityTag('abc123'),
     *     new EntityTag('def456', true)
     * );
     *
     * // If-None-Match: "abc123", W/"def456"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join entity tags.
     *
     * @param \FireHub\Adapter\HTTP\Header\EntityTag\Tag|\FireHub\Adapter\HTTP\Header\EntityTag\Range $tag <p>
     * First entity tag or wildcard.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\EntityTag\Tag ...$additional <p>
     * Additional entity tags.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidEntityTagException If a wildcard is combined with entity tags.
     *
     * @return self Updated request header collection.
     */
    public function ifNoneMatch (EntityTag|EntityTagRange $tag, EntityTag ...$additional):self {

        if ($tag instanceof EntityTagRange) {

            if ($additional !== [])
                throw new InvalidEntityTagException(
                    'An entity tag wildcard cannot be combined with additional entity tags.'
                );

            return $this->replace(
                'If-None-Match',
                new Value($tag->value)
            );

        }

        $values = [$tag->value()];

        foreach ($additional as $item)
            $values[] = $item->value();

        return $this->replace(
            'If-None-Match',
            new Value(
                Runtime\Str\SB\Delimiter::implode($values, ', ')
            )
        );

    }

    /**
     * ### Sets the If-Modified-Since request header
     *
     * Makes the request conditional on the target resource having been modified after the specified date.
     *
     * <code>
     * $headers = Headers::empty()->ifModifiedSince(
     *     new Date(
     *         DateTime::from('2026-10-29 10:00:00')
     *     )
     * );
     *
     * // If-Modified-Since: Thu, 29 Oct 2026 10:00:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Date::value() To serialize the HTTP date.
     *
     * @param \FireHub\Adapter\HTTP\Header\Date $date <p>
     * Modification date.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function ifModifiedSince (Date $date):self {

        return $this->replace(
            'If-Modified-Since',
            new Value($date->value())
        );

    }

    /**
     * ### Sets the If-Unmodified-Since request header
     *
     * Makes the request conditional on the target resource not having been modified after the specified date.
     *
     * Commonly used to prevent modifications to a resource that has changed since the client last retrieved it.
     *
     * <code>
     * $headers = Headers::empty()->ifUnmodifiedSince(
     *     new Date(
     *         DateTime::from('2026-10-29 10:00:00')
     *     )
     * );
     *
     * // If-Unmodified-Since: Thu, 29 Oct 2026 10:00:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Date::value() To serialize the HTTP date.
     *
     * @param \FireHub\Adapter\HTTP\Header\Date $date <p>
     * Last known modification date.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function ifUnmodifiedSince (Date $date):self {

        return $this->replace(
            'If-Unmodified-Since',
            new Value($date->value())
        );

    }

    /**
     * ### Sets the Referer request header
     *
     * Specifies the URI reference of the resource from which the target URI was obtained.
     *
     * URI fragments and user information are automatically removed to comply with HTTP Referer header requirements.
     *
     * <code>
     * $headers = Headers::empty()->referer(
     *     Uri::parse('https://example.com/products#details')
     * );
     *
     * // Referer: https://example.com/products
     *
     * $headers = Headers::empty()->referer(
     *     Uri::parse('https://user:password@example.com/products')
     * );
     *
     * // Referer: https://example.com/products
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::withoutFragment() To remove the URI fragment.
     * @uses \FireHub\Foundation\Uri::authority() To retrieve the URI authority.
     * @uses \FireHub\Foundation\Uri\Component\Authority::withoutUserInfo() To remove user information.
     * @uses \FireHub\Foundation\Uri::withAuthority() To replace the URI authority.
     * @uses \FireHub\Foundation\Uri::value() To serialize the URI reference.
     *
     * @param \FireHub\Foundation\Uri $referer <p>
     * URI reference identifying the referring resource.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI component combination is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated request header collection.
     */
    public function referer (Uri $referer):self {

        $referer = $referer->withoutFragment();

        $authority = $referer->authority();

        if ($authority !== null && $authority->userInfo() !== null)
            $referer = $referer->withAuthority(
                $authority->withoutUserInfo()
            );

        return $this->replace(
            'Referer',
            new Value($referer->value())
        );

    }

    /**
     * ### Sets the Origin request header
     *
     * Specifies the origin that initiated the HTTP request.
     *
     * The origin consists of the URI scheme, host, and optional port. Path, query, fragment, and user information
     * are excluded.
     *
     * <code>
     * $headers = Headers::empty()->origin(
     *     Uri::parse('https://example.com/products?page=2#details')
     * );
     *
     * // Origin: https://example.com
     *
     * $headers = Headers::empty()->origin(
     *     Uri::parse('http://localhost:8080/api/users')
     * );
     *
     * // Origin: http://localhost:8080
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::scheme() To retrieve the URI scheme.
     * @uses \FireHub\Foundation\Uri::authority() To retrieve the URI authority.
     * @uses \FireHub\Foundation\Uri\Component\Authority::withoutUserInfo() To remove user information.
     * @uses \FireHub\Foundation\Uri::withAuthority() To replace the URI authority.
     * @uses \FireHub\Foundation\Uri::withPath() To remove the URI path.
     * @uses \FireHub\Foundation\Uri::withoutQuery() To remove the URI query.
     * @uses \FireHub\Foundation\Uri::withoutFragment() To remove the URI fragment.
     * @uses \FireHub\Foundation\Uri::value() To serialize the origin.
     *
     * @param \FireHub\Foundation\Uri $origin <p>
     * URI identifying the origin of the request.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidOriginException If the URI cannot represent an HTTP origin.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI component combination is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated request header collection.
     */
    public function origin (Uri $origin):self {

        $scheme = $origin->scheme()?->normalized();
        $authority = $origin->authority();

        if (
            ($scheme !== KnownScheme::HTTP->value && $scheme !== KnownScheme::HTTPS->value)
            || $origin->authority() === null
            || $origin->authority()->host()->value() === ''
        ) throw new InvalidOriginException;

        /** @var \FireHub\Foundation\Uri\Component\Authority $authority */
        return $this->replace(
            'Origin',
            new Value(
                $origin
                    ->withAuthority($authority->withoutUserInfo())
                    ->withPath(new Path(''))
                    ->withoutQuery()
                    ->withoutFragment()
                    ->normalize()
                    ->value()
            )
        );

    }

    /**
     * ### Sets the Host request header
     *
     * <code>
     * // Set the Host header.
     * $headers = Headers::empty()->host(
     *     new Authority(
     *         new Host('localhost'),
     *         new Port(222)
     *     )
     * );
     *
     * // Host: localhost:222
     *
     * // Get the parsed Host header.
     * $authority = $headers->getHost();
     *
     * $authority?->host()->value(); // localhost
     * $authority?->port()?->value(); // 222
     *
     * // Host without a port.
     * $headers = Headers::empty()->host(
     *     new Authority(new Host('example.com'))
     * );
     *
     * // Host: example.com
     *
     * // IPv6 host with a port.
     * $headers = Headers::empty()->host(
     *     new Authority(
     *         new Host('[::1]'),
     *         new Port(8080)
     *     )
     * );
     *
     * // Host: [::1]:8080
     *
     * // Missing Host header.
     * Headers::empty()->getHost(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Authority::host() To retrieve the host component.
     * @uses \FireHub\Foundation\Uri\Component\Authority::userInfo() To retrieve the user information component.
     *
     * @param \FireHub\Foundation\Uri\Component\Authority $authority <p>
     * HTTP target authority.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidHostException If the authority is invalid.
     *
     * @return self Updated request header collection.
     */
    public function host (Authority $authority):self {

        if (
            $authority->userInfo() !== null
            || $authority->host()->isEmpty()
        ) throw new InvalidHostException(
            'Invalid HTTP Host authority.'
        );

        return $this->replace(
            'Host',
            new Value($authority->value())
        );

    }

    /**
     * ### Sets the Cookie request header
     *
     * Serializes one or more request cookies into a single Cookie header field.
     *
     * Replaces all previously configured Cookie headers. Preserves cookie order and duplicate cookie names.
     *
     * <code>
     * $headers = Headers::empty()->cookie(
     *     new RequestCookie('session', 'abc123'),
     *     new RequestCookie('theme', 'dark')
     * );
     *
     * // Cookie: session=abc123; theme=dark
     *
     * // Duplicate cookie names are preserved.
     * $headers = Headers::empty()->cookie(
     *     new RequestCookie('session', 'abc123'),
     *     new RequestCookie('session', 'xyz789')
     * );
     *
     * // Cookie: session=abc123; session=xyz789
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Request::value() To serialize request cookies.
     * @uses \FireHub\Adapter\HTTP\Request\Headers::replace() To replace the Cookie header.
     *
     * @param \FireHub\Adapter\HTTP\Cookie\Request<string> $cookie <p>
     * First request cookie.
     * </p>
     * @param \FireHub\Adapter\HTTP\Cookie\Request<string> ...$additional <p>
     * Additional request cookies.
     * </p>
     *
     * @return self Updated request header collection.
     */
    public function cookie (RequestCookie $cookie, RequestCookie ...$additional):self {

        return $this->replace(
            'Cookie',
            new Value(
                new Cookie($cookie, ...$additional)->value()
            )
        );

    }

    /**
     * ### Checks whether an HTTP request header exists
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Content-Type'), new Value('application/json'));
     *
     * $headers->has(new Name('Content-Type')); // true
     * $headers->has(new Name('content-type')); // true
     * $headers->has(new Name('CONTENT-TYPE')); // true
     * $headers->has(new Name('Authorization')); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::has() Checks whether a custom HTTP message header exists.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * HTTP header name.
     * </p>
     *
     * @return bool True if the header exists.
     */
    public function has (Name $name):bool {

        return $this->headers->has($name);

    }

    /**
     * ### Gets HTTP request header values
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept'), new Value('application/json'))
     *     ->add(new Name('Accept'), new Value('text/html'));
     *
     * $values = $headers->get(new Name('accept'));
     *
     * foreach ($values as $value)
     *     echo $value->value() . PHP_EOL;
     *
     * // application/json
     * // text/html
     *
     * // Missing headers return an empty list.
     * $headers->get(new Name('Authorization')); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() Gets all values for a custom HTTP message header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * HTTP header name.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Value<string>> Matching header values.
     */
    public function get (Name $name):array {

        return $this->headers->get($name);

    }

    /**
     * ### Adds a new HTTP request header
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('X-Request-ID'), new Value('123'))
     *     ->add(new Name('X-Request-ID'), new Value('456'));
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
    public function add (Name $name, Value $value):self {

        return new self(
            $this->headers->add($name, $value)
        );

    }

    /**
     * ### Gets all HTTP request header fields
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept'), new Value('application/json'))
     *     ->add(new Name('X-Request-ID'), new Value('123'));
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
     * ### Gets the Authorization request header
     *
     * Parses the Authorization header into strongly typed authentication credentials.
     *
     * Returns null if the Authorization header is not present.
     *
     * <code>
     * $headers = Headers::empty()->authorization(
     *     new Bearer('my-access-token')
     * );
     *
     * $credentials = $headers->getAuthorization();
     *
     * // Bearer
     *
     * Headers::empty()->getAuthorization();
     *
     * // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve the Authorization header values.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To validate the number of Authorization header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Authorization\Parser::parse() To parse authentication credentials.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCredentialsFormatException If multiple Authorization headers
     * are present or the credentials are malformed.
     * @throws \FireHub\Core\Exception\FireHubException If a credential validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a credential validation exception is not a
     * FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Authorization\Credentials Parsed authentication credentials, or null
     * if the header is absent.
     */
    public function getAuthorization ():?Credentials {

        $values = $this->headers->get(
            new Name('Authorization')
        );

        if ($values === []) return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new InvalidCredentialsFormatException(
                'Multiple Authorization header fields are not allowed.'
            );

        return AuthorizationParser::parse(
            $values[0]->value()
        );

    }

    /**
     * ### Gets the parsed Connection request header
     *
     * Returns standard and custom HTTP connection options.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Connection\Type;
     * use FireHub\Adapter\HTTP\Header\Connection\Custom;
     *
     * $options = $headers->getConnection();
     *
     * foreach ($options as $option) {
     *
     *     if ($option === Type::KEEP_ALIVE) {
     *         // Keep-alive option.
     *     }
     *
     *     if ($option instanceof Custom) {
     *         // Custom connection option.
     *     }
     *
     * }
     *
     * // Missing Connection header.
     * Headers::empty()->getConnection(); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Connection header values.
     * @uses \FireHub\Adapter\HTTP\Header\Connection\Parser::parse() To parse connection options.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Connection\Type|\FireHub\Adapter\HTTP\Header\Connection\Custom>
     * Parsed connection options.
     */
    public function getConnection ():array {

        $options = [];

        foreach ($this->headers->get(new Name('Connection')) as $value)
            foreach (ConnectionParser::parse($value->value()) as $option)
                $options[] = $option;

        return $options;

    }

    /**
     * ### Gets the parsed Upgrade request header
     *
     * <code>
     * $protocols = $headers->getUpgrade();
     *
     * foreach ($protocols as $protocol)
     *     echo $protocol->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Upgrade\Protocol> Parsed upgrade protocols.
     */
    public function getUpgrade ():array {

        $protocols = [];

        foreach ($this->headers->get(new Name('Upgrade')) as $value)
            foreach (UpgradeParser::parse($value->value()) as $protocol)
                $protocols[] = $protocol;

        return $protocols;

    }

    /**
     * ### Gets the parsed Content-Length request header
     *
     * Returns the content length as a validated value object.
     *
     * Identical repeated values are accepted, while conflicting values are rejected.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Content-Length'), new Value('1024'));
     *
     * $length = $headers->getContentLength();
     *
     * echo $length?->length(); // 1024
     *
     * // Missing Content-Length header.
     * Headers::empty()->getContentLength(); // null
     *
     * // Identical repeated values.
     * $headers = Headers::empty()
     *     ->add(new Name('Content-Length'), new Value('1024'))
     *     ->add(new Name('Content-Length'), new Value('1024'));
     *
     * echo $headers->getContentLength()?->length(); // 1024
     *
     * // Conflicting values throw InvalidContentLengthException.
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve the header values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Length::parse() To parse the content length.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Length::length() To compare parsed lengths.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Length::value() To retrieve the header value.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the header value.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the header value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentLengthException If the header contains invalid or
     * conflicting values.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Length Parsed content length, or null if absent.
     */
    public function getContentLength ():?Length {

        $values = $this->headers->get(
            new Name('Content-Length')
        );

        if ($values === []) return null;

        $length = null;

        foreach ($values as $value) {

            foreach (Runtime\Str\SB\Delimiter::explode($value->value(), ',') as $part) {

                $current = Length::parse(Runtime\Str\SB\Transform::trim($part, characters: " \t"));

                if (
                    $length !== null
                    && $length->length() !== $current->length()
                ) throw new InvalidContentLengthException(
                    'Conflicting Content-Length header values.'
                );

                $length = $current;

            }

        }

        return $length;

    }

    /**
     * ### Gets the parsed Content-Type request header
     *
     * Returns the media type and its parameters as a validated ContentType value object.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(
     *         new Name('Content-Type'),
     *         new Value('application/json; charset=utf-8')
     *     );
     *
     * $content_type = $headers->getContentType();
     *
     * echo $content_type?->value();
     *
     * // application/json; charset=utf-8
     *
     * // Missing Content-Type header.
     * Headers::empty()->getContentType(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve the header values.
     * @uses \FireHub\Adapter\HTTP\Header\Media\ContentType::parse() To parse the media type.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check if the media type has parameters.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the Content-Type header is invalid.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Media\ContentType<non-empty-string> Parsed content type, or null if
     * absent.
     */
    public function getContentType ():?ContentType {

        $values = $this->headers->get(
            new Name('Content-Type')
        );

        if ($values === []) return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new InvalidMediaTypeException(
                'Multiple Content-Type headers are not allowed.'
            );

        return ContentType::parse(
            $values[0]->value()
        );

    }

    /**
     * ### Gets the Content-Encoding request header
     *
     * Returns content codings in the order in which they were applied.
     *
     * <code>
     *
     * $headers = Headers::empty()->contentEncoding(
     *     Type::GZIP,
     *     Type::BR
     * );
     *
     * $encodings = $headers->getContentEncoding();
     *
     * // [Type::GZIP, Type::BR]
     *
     * Headers::empty()->getContentEncoding();
     *
     * // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To parse content encoding types.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To match the content encoding.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the content encoding to lowercase.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If a content coding is invalid.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Encoding\Type|\FireHub\Adapter\HTTP\Header\Encoding\Custom<non-empty-string>>
     * Parsed content codings.
     */
    public function getContentEncoding ():array {

        $encodings = [];

        foreach (
            $this->headers->get(new Name('Content-Encoding'))
            as $value
        ) {

            foreach (ListingParser::parse($value->value()) as $encoding) {

                if (!Runtime\Str\SB\Regex::match(
                    "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
                    $encoding
                )) throw new InvalidParameterValueException(
                    'Invalid Content-Encoding header value.'
                );

                $normalized = Runtime\Str\SB\Casing::toLower($encoding);

                $type = EncodingType::tryFrom($normalized);

                $encodings[] = $type ?? new EncodingCustom($encoding);

            }

        }

        return $encodings;

    }

    /**
     * ### Gets the Content-Language request header
     *
     * Returns the natural languages of the intended audience for the representation.
     *
     * Supports multiple Content-Language header fields and comma-separated language tags while preserving their
     * original order.
     *
     * <code>
     * $headers = Headers::empty()->contentLanguage(
     *     Type::HR_HR,
     *     Type::EN_US,
     *     new Custom('bs-BA')
     * );
     *
     * // Content-Language: hr-HR, en-US, bs-BA
     *
     * $languages = $headers->getContentLanguage();
     *
     * // [Type::HR_HR, Type::EN_US, new Custom('bs-BA')]
     *
     * Headers::empty()->getContentLanguage();
     *
     * // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Content-Language header values.
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To parse language lists.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize language tags.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidLanguageException If a language tag is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If list syntax is invalid.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Language\Type|\FireHub\Adapter\HTTP\Header\Language\Custom<non-empty-string>>
     * Parsed content languages.
     */
    public function getContentLanguage ():array {

        $languages = [];

        foreach ($this->headers->get(new Name('Content-Language')) as $value)
            foreach (ListingParser::parse($value->value()) as $language) {

                $normalized = Runtime\Str\SB\Casing::toLower($language);

                $type = null;

                foreach (LanguageType::cases() as $case)
                    if (Runtime\Str\SB\Casing::toLower($case->value) === $normalized) {

                        $type = $case;

                        break;

                    }

                $languages[] = $type ?? new LanguageCustom($language);

            }

        return $languages;

    }

    /**
     * ### Gets the Content-Location request header
     *
     * Returns the URI identifying the representation enclosed in the HTTP request message.
     *
     * <code>
     * $headers = Headers::empty()->add(
     *     new Name('Content-Location'),
     *     new Value('/documents/report.pdf')
     * );
     *
     * $uri = $headers->getContentLocation();
     *
     * // /documents/report.pdf
     *
     * Headers::empty()->getContentLocation();
     *
     * // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Content-Location header values.
     * @uses \FireHub\Foundation\Uri::parse() To parse the URI.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check if the header has a value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If multiple Content-Location fields are present.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI is invalid.
     *
     * @return null|\FireHub\Foundation\Uri Parsed Content-Location URI, or null if absent.
     */
    public function getContentLocation ():?Uri {

        $values = $this->headers->get(
            new Name('Content-Location')
        );

        if ($values === []) return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new InvalidParameterValueException(
                'Content-Location header must contain exactly one URI.'
            );

        $value = $values[0]->value();

        if ($value === '')
            throw new InvalidParameterValueException(
                'Content-Location header cannot be empty.'
            );

        return Uri::parse($value);

    }

    /**
     * ### Gets the parsed "Accept request header"
     *
     * Returns the media preferences accepted by the HTTP client.
     *
     * Supports multiple Accept header fields and comma-separated medi preferences while preserving their original
     * order.
     *
     * Commas inside quoted parameter values are not treated as separators.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(
     *         new Name('Accept'),
     *         new Value('text/html, application/json;q=0.9')
     *     );
     *
     * $preferences = $headers->getAccept();
     *
     * $preferences[0]->media(); // Type::TEXT_HTML
     * $preferences[1]->media(); // Type::APPLICATION_JSON
     * $preferences[1]->quality()->value(); // 0.9
     *
     * // Missing Accept header.
     * Headers::empty()->getAccept(); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept header values.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Preference::parse() To parse individual media preferences.
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To parse the Accept header.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the Accept header is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If parameter syntax is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaPreferenceExceptionQuality If quality is invalid.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Media\Preference<non-empty-string>> Parsed media preferences.
     */
    public function getAccept ():array {

        $preferences = [];

        foreach ($this->headers->get(new Name('Accept')) as $value)
            foreach (ListingParser::parse($value->value()) as $member)
                $preferences[] = MediaPreference::parse($member);

        return $preferences;

    }

    /**
     * ### Gets the parsed Accept-Encoding request header
     *
     * Returns the content codings accepted by the HTTP client.
     *
     * Supports multiple Accept-Encoding header fields and comma-separated encoding preferences while preserving
     * their original order.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(
     *         new Name('Accept-Encoding'),
     *         new Value('gzip, deflate, br, zstd')
     *     );
     *
     * $preferences = $headers->getAcceptEncoding();
     *
     * $preferences[0]->encoding(); // Type::GZIP
     * $preferences[1]->encoding(); // Type::DEFLATE
     * $preferences[2]->encoding(); // Type::BR
     * $preferences[3]->encoding(); // Type::ZSTD
     *
     * // Missing Accept-Encoding header.
     * Headers::empty()->getAcceptEncoding(); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept-Encoding header values.
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To split comma-separated preferences.
     * @uses \FireHub\Adapter\HTTP\Header\Encoding\Preference::parse() To parse encoding preferences.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidEncodingException If an encoding preference is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If the header syntax is invalid.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Encoding\Preference<non-empty-string>> Parsed encoding preferences.
     */
    public function getAcceptEncoding ():array {

        $preferences = [];

        foreach ($this->headers->get(new Name('Accept-Encoding')) as $value)
            foreach (ListingParser::parse($value->value()) as $member)
                $preferences[] = EncodingPreference::parse($member);

        return $preferences;

    }

    /**
     * ### Gets the parsed Accept-Language request header
     *
     * Returns the language preferences accepted by the HTTP client.
     *
     * Supports multiple Accept-Language header fields and comma-separated language preferences while preserving
     * their original order.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(
     *         new Name('Accept-Language'),
     *         new Value('en-US,en;q=0.9')
     *     );
     *
     * $preferences = $headers->getAcceptLanguage();
     *
     * $preferences[0]->language(); // Type::EN_US
     * $preferences[0]->quality()->value(); // 1.0
     *
     * $preferences[1]->language(); // Type::EN
     * $preferences[1]->quality()->value(); // 0.9
     *
     * // Missing Accept-Language header.
     * Headers::empty()->getAcceptLanguage(); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Accept-Language header values.
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To split comma-separated preferences.
     * @uses \FireHub\Adapter\HTTP\Header\Language\Preference::parse() To parse language preferences.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidLanguageException If the language preference is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If parameter syntax is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaPreferenceExceptionQuality If the quality factor is
     * invalid.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Language\Preference<non-empty-string>> Parsed language preferences.
     */
    public function getAcceptLanguage ():array {

        $preferences = [];

        foreach ($this->headers->get(new Name('Accept-Language')) as $value)
            foreach (ListingParser::parse($value->value()) as $member)
                $preferences[] = LanguagePreference::parse($member);

        return $preferences;

    }

    /**
     * ### Gets the parsed User-Agent request header
     *
     * Returns the product and comment components of the HTTP User-Agent header.
     *
     * <code>
     * $components = $headers->getUserAgent();
     *
     * foreach ($components ?? [] as $component)
     *     echo $component->value() . PHP_EOL;
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve User-Agent header values.
     * @uses \FireHub\Adapter\HTTP\Header\UserAgent\Parser::parse() To parse the User-Agent header.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check if the header contains multiple components.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException If the syntax is invalid.
     *
     * @return null|non-empty-list<\FireHub\Adapter\HTTP\Header\UserAgent\Product|\FireHub\Adapter\HTTP\Header\UserAgent\Comment>
     * Parsed User-Agent components, or null if absent.
     */
    public function getUserAgent ():?array {

        $values = $this->headers->get(
            new Name('User-Agent')
        );

        if ($values === []) return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new \FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException(
                'Multiple User-Agent header fields are not allowed.'
            );

        return UserAgentParser::parse(
            $values[0]->value()
        );

    }

    /**
     * ### Gets the parsed Host request header
     *
     * Returns the HTTP request target authority, including the host and optional network port.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(
     *         new Name('Host'),
     *         new Value('localhost:222')
     *     );
     *
     * $authority = $headers->getHost();
     *
     * $authority?->host()->value(); // localhost
     * $authority?->port()?->value(); // 222
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Host header values.
     * @uses \FireHub\Foundation\Uri\Component\Authority::parse() To parse the host and port.
     * @uses \FireHub\Foundation\Uri\Component\Authority::host() To retrieve the host component.
     * @uses \FireHub\Foundation\Uri\Component\Authority::port() To retrieve the port component.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check if the header contains a port.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check if the header contains a port.
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the header contains a port.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidHostException If the Host header is invalid.
     *
     * @return null|\FireHub\Foundation\Uri\Component\Authority Parsed authority, or null if absent.
     */
    public function getHost ():?Authority {

        $values = $this->headers->get(
            new Name('Host')
        );

        if ($values === []) return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new InvalidHostException(
                'Multiple Host header fields are not allowed.'
            );

        $value = $values[0]->value();

        if ($value === '')
            throw new InvalidHostException(
                'Host header cannot be empty.'
            );

        if (Runtime\Str\SB\Regex::match('/[@\/?#\s]/', $value))
            throw new InvalidHostException(
                'Invalid HTTP Host header syntax.'
            );

        if (Runtime\Str\SB\Search::endsWith(':', $value))
            throw new InvalidHostException(
                'Host header port cannot be empty.'
            );

        $authority = Authority::parse($value);

        if ($authority->host()->isEmpty())
            throw new InvalidHostException(
                'Host header cannot contain an empty hostname.'
            );

        if ($authority->userInfo() !== null)
            throw new InvalidHostException(
                'Host header cannot contain user information.'
            );

        return $authority;

    }

    /**
     * ### Gets the Cache-Control request header
     *
     * Parses all Cache-Control request header fields into strongly typed request cache directives.
     *
     * Multiple Cache-Control header fields are processed in their original order, and comma-separated directives are
     * parsed individually.
     *
     * Returns an empty list if the Cache-Control header is absent.
     *
     * <code>
     * $headers = Headers::empty()->cacheControl(
     *     RequestDirective::noCache(),
     *     RequestDirective::maxAge(3600)
     * );
     *
     * $directives = $headers->getCacheControl();
     *
     * // [
     * //     RequestDirective::noCache(),
     * //     RequestDirective::maxAge(3600)
     * // ]
     *
     * Headers::empty()->getCacheControl();
     *
     * // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Cache-Control header values.
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\RequestParser::parse() To parse request cache directives.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a cache directive is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If the directive list is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If a validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a validation exception is not a FireHubException.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective|\FireHub\Adapter\HTTP\Header\CacheControl\Custom> Parsed request cache directives.
     */
    public function getCacheControl ():array {

        $directives = [];

        foreach ($this->headers->get(new Name('Cache-Control')) as $value)
            foreach (CacheControlParser::parse($value->value()) as $directive)
                $directives[] = $directive;

        return $directives;

    }

    /**
     * ### Gets the Cookie request header
     *
     * Parses all Cookie header fields and returns their cookies as a structured collection.
     *
     * Preserves cookie order and duplicate cookie names.
     *
     * Returns an empty cookie collection when the header is absent.
     *
     * <code>
     * $headers = Headers::empty()->cookie(
     *     new RequestCookie('session', 'abc123'),
     *     new RequestCookie('theme', 'dark')
     * );
     *
     * $cookies = $headers->getCookie();
     *
     * $cookies->has('session'); // true
     * $cookies->get('session'); // List of matching cookies
     * $cookies->cookies();      // All request cookies
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Headers::get() To retrieve Cookie header values.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Request::parse() To parse Cookie header values.
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie\Request Parsed request cookies.
     */
    public function getCookie ():Cookie {

        $cookies = [];

        foreach ($this->get(new Name('Cookie')) as $value) {

            $parsed = Cookie::parse($value->value());

            foreach ($parsed->cookies() as $cookie)
                $cookies[] = $cookie;

        }

        return new Cookie(...$cookies);

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