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
    Authorization\Challenge,
    CacheControl\Custom, CacheControl\ResponseDirective,
    Content\Length,
    Cookie\Set as SetCookie,
    Encoding\Custom as EncodingCustom, Encoding\Type as EncodingType,
    EntityTag\Tag as EntityTag,
    Language\Custom as LanguageCustom, Language\Type as LanguageType,
    Media\ContentType, Media\Custom as MediaCustom, Media\Parameter as MediaParameter,
    Media\Type as MediaType,
    AcceptRanges, Age, Allow, ContentDisposition, ContentRange, ContentSecurityPolicy, Date, Name, RetryAfter,
    StrictTransportSecurity, Value, Vary
};
use FireHub\Adapter\HTTP\Cookie\Response as ResponseCookie;
use FireHub\Adapter\HTTP\Request\Method;
use FireHub\Foundation\Uri;
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
     * ### Sets the WWW-Authenticate response header
     *
     * Advertises one or more authentication challenges supported by the server.
     *
     * Each challenge is stored as a separate WWW-Authenticate header field. Replaces any previously configured
     * WWW-Authenticate headers.
     *
     * <code>
     * $headers = Headers::empty()->wwwAuthenticate(
     *     new Basic('FireHub API')
     * );
     *
     * // WWW-Authenticate: Basic realm="FireHub API", charset="UTF-8"
     *
     * $headers = Headers::empty()->wwwAuthenticate(
     *     new Basic('FireHub API'),
     *     new Bearer('FireHub API')
     * );
     *
     * // WWW-Authenticate: Basic realm="FireHub API", charset="UTF-8"
     * // WWW-Authenticate: Bearer realm="FireHub API"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Authorization\Challenge::value() To serialize authentication challenges.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace existing WWW-Authenticate headers.
     *
     * @param \FireHub\Adapter\HTTP\Header\Authorization\Challenge $challenge <p>
     * First authentication challenge.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Authorization\Challenge ...$additional <p>
     * Additional authentication challenges.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function wwwAuthenticate (Challenge $challenge, Challenge ...$additional):self {

        $values = [];

        foreach ([$challenge, ...$additional] as $item)
            $values[] = new Value($item->value());

        return $this->replace(
            'WWW-Authenticate',
            ...$values
        );

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
     * ### Sets the X-Content-Type-Options response header
     *
     * Prevents user agents from MIME-sniffing response content and instructs them to respect the declared Content-Type.
     *
     * Sets the only standardized directive, "nosniff".
     *
     * Replaces any previously configured X-Content-Type-Options header.
     *
     * <code>
     * $headers = Headers::empty()->xContentTypeOptions();
     *
     * // X-Content-Type-Options: nosniff
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the X-Content-Type-Options header.
     *
     * @return self Updated response header collection.
     */
    public function xContentTypeOptions ():self {

        return $this->replace(
            'X-Content-Type-Options',
            new Value('nosniff')
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
     * ### Sets the Content-Disposition response header
     *
     * Specifies whether response content should be displayed inline or handled as an attachment.
     *
     * Supports UTF-8 filenames through the filename* parameter.
     *
     * Replaces any previously configured Content-Disposition header.
     *
     * <code>
     * $headers = Headers::empty()->contentDisposition(
     *     new ContentDisposition(
     *         Type::ATTACHMENT,
     *         'report.pdf'
     *     )
     * );
     *
     * // Content-Disposition: attachment; filename="report.pdf"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentDisposition::value() To serialize the disposition.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Content-Disposition header.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentDisposition $disposition <p>
     * Content disposition definition.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function contentDisposition (ContentDisposition $disposition):self {

        return $this->replace(
            'Content-Disposition',
            new Value($disposition->value())
        );

    }

    /**
     * ### Sets the Content-Range response header
     *
     * Identifies the byte range represented by the response content, or indicates that the requested range could not
     * be satisfied.
     *
     * Replaces any previously configured Content-Range header.
     *
     * <code>
     * $headers = Headers::empty()->contentRange(
     *     ContentRange::satisfied(
     *         new ByteRange(0, 1023),
     *         4096
     *     )
     * );
     *
     * // Content-Range: bytes 0-1023/4096
     *
     * $headers = Headers::empty()->contentRange(
     *     ContentRange::unsatisfied(4096)
     * );
     *
     * // Content-Range: bytes /4096'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentRange::value() To serialize the Content-Range header.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Content-Range header.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentRange $range <p>
     * Content range definition.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function contentRange (ContentRange $range):self {

        return $this->replace(
            'Content-Range',
            new Value($range->value())
        );

    }

    /**
     * ### Sets the Content-Security-Policy response header
     *
     * Defines an enforced security policy controlling resource loading and other supported browser behaviors.
     *
     * <code>
     * $nonce = Nonce::generate();
     *
     * $policy = ContentSecurityPolicy::create()
     *     ->defaultSrc(Source::SELF)
     *     ->scriptSrc(
     *         Source::SELF,
     *         new Host('cdn.example.com', 'https'),
     *         $nonce
     *     )
     *     ->styleSrc(Source::SELF)
     *     ->imgSrc(
     *         Source::SELF,
     *         new Scheme('data')
     *     )
     *     ->objectSrc(Source::NONE)
     *     ->frameAncestors(Source::NONE)
     *     ->upgradeInsecureRequests();
     *
     * $headers = Headers::empty()->contentSecurityPolicy($policy);
     *
     * // Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.example.com 'nonce-...'; style-src 'self'; img-src 'self' data:; object-src 'none'; frame-ancestors 'none'; upgrade-insecure-requests
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy $policy <p>
     * Security policy.
     * </p>
     *
     * @return self Updated response headers.
     */
    public function contentSecurityPolicy (ContentSecurityPolicy $policy):self {

        return $this->replace(
            'Content-Security-Policy',
            new Value($policy->value())
        );

    }

    /**
     * ### Sets the Content-Security-Policy-Report-Only response header
     *
     * Reports violations of a security policy without enforcing its supported restrictions.
     *
     * <code>
     * $policy = ContentSecurityPolicy::create()
     *     ->defaultSrc(Source::SELF)
     *     ->scriptSrc(
     *         Source::SELF,
     *         new Host('cdn.example.com', 'https')
     *     )
     *     ->styleSrc(Source::SELF)
     *     ->objectSrc(Source::NONE);
     *
     * $headers = Headers::empty()->contentSecurityPolicyReportOnly($policy);
     *
     * // Content-Security-Policy-Report-Only: default-src 'self'; script-src 'self' https://cdn.example.com; style-src 'self'; object-src 'none'
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy $policy <p>
     * Reporting policy.
     * </p>
     *
     * @return self Updated response headers.
     */
    public function contentSecurityPolicyReportOnly (ContentSecurityPolicy $policy):self {

        return $this->replace(
            'Content-Security-Policy-Report-Only',
            new Value($policy->value())
        );

    }

    /**
     * ### Adds an HTTP response cookie
     *
     * Appends a Set-Cookie header without replacing previously configured cookies.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->setCookie(
     *         new ResponseCookie(
     *             name: 'session',
     *             value: 'abc123',
     *             path: new Path('/'),
     *             max_age: 3600
     *         )
     *     )
     *     ->setCookie(
     *         new ResponseCookie(
     *             name: 'theme',
     *             value: 'dark',
     *             path: new Path('/'),
     *             http_only: false
     *         )
     *     );
     *
     * // Set-Cookie: session=abc123; Path=/; Max-Age=3600; Secure; HttpOnly; SameSite=Lax
     * // Set-Cookie: theme=dark; Path=/; Secure; SameSite=Lax
     *
     * // Each cookie is stored as a separate Set-Cookie header field.
     * // Existing Set-Cookie headers are preserved.
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\SetCookie::value() To serialize the cookie.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::add() To append the header.
     *
     * @param \FireHub\Adapter\HTTP\Cookie\Response<string> $cookie <p>
     * Response cookie.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function setCookie (ResponseCookie $cookie):self {

        return new self(
            $this->headers->add(
                new Name('Set-Cookie'),
                new Value(
                    new SetCookie($cookie)->value())
            )
        );

    }

    /**
     * ### Sets the ETag response header
     *
     * Specifies an entity tag identifying a particular version of the selected resource representation.
     *
     * Entity tags may use strong or weak comparison semantics.
     *
     * Replaces any previously configured ETag header.
     *
     * <code>
     * $headers = Headers::empty()->etag(
     *     new EntityTag('version-123')
     * );
     *
     * // ETag: "version-123"
     *
     * $headers = Headers::empty()->etag(
     *     new EntityTag('version-123', true)
     * );
     *
     * // ETag: W/"version-123"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\EntityTag\Tag::value() To serialize the entity tag.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the ETag header.
     *
     * @param \FireHub\Adapter\HTTP\Header\EntityTag\Tag $tag <p>
     * Entity tag identifying the selected representation.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function etag (EntityTag $tag):self {

        return $this->replace(
            'ETag',
            new Value($tag->value())
        );

    }

    /**
     * ### Sets the Last-Modified response header
     *
     * Specifies the date and time at which the origin server believes the selected resource representation was last
     * modified.
     *
     * The date is serialized in HTTP-date format using GMT.
     *
     * Replaces any previously configured Last-Modified header.
     *
     * <code>
     * $headers = Headers::empty()->lastModified(
     *     new Date(
     *         DateTime::from('2026-10-29 10:00:00')
     *     )
     * );
     *
     * // Last-Modified: Thu, 29 Oct 2026 10:00:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Date::value() To serialize the HTTP date.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Last-Modified header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Date $date <p>
     * Date and time when the selected representation was last modified.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function lastModified (Date $date):self {

        return $this->replace(
            'Last-Modified',
            new Value($date->value())
        );

    }

    /**
     * ### Sets the Expires response header
     *
     * Specifies the date and time after which the response is considered stale.
     *
     * The date is serialized using the HTTP IMF-fixdate format.
     *
     * Cache-Control directives such as max-age and s-maxage take precedence over Expires when determining response
     * freshness.
     *
     * Replaces any previously configured Expires header.
     *
     * <code>
     * $headers = Headers::empty()->expires(
     *     new Date($date_time)
     * );
     *
     * // Expires: Tue, 03 Nov 2026 10:00:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Date::value() To serialize the expiration date.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Expires header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Date $expires <p>
     * Date and time after which the response is considered stale.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function expires (Date $expires):self {

        return $this->replace(
            'Expires',
            new Value($expires->value())
        );

    }

    /**
     * ### Sets the Age response header
     *
     * Specifies the estimated age of the response in seconds.
     *
     * The Age field is primarily used by HTTP caches to communicate how long a response has existed since its
     * generation or validation.
     *
     * Replaces any previously configured Age header.
     *
     * <code>
     * $headers = Headers::empty()->age(
     *     new Age(120)
     * );
     *
     * // Age: 120
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Age::value() To serialize the Age header.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Age header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Age $age <p>
     * Estimated response age.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function age (Age $age):self {

        return $this->replace(
            'Age',
            new Value($age->value())
        );

    }

    /**
     * ### Sets the Location response header
     *
     * Specifies a URI reference identifying the target resource of a redirect or the primary resource created by a
     * successful request.
     *
     * Supports both absolute and relative URI references.
     *
     * Replaces any previously configured Location header.
     *
     * <code>
     * $headers = Headers::empty()->location(
     *     Uri::parse('https://example.com/dashboard')
     * );
     *
     * // Location: https://example.com/dashboard
     *
     * $headers = Headers::empty()->location(
     *     Uri::parse('/users/123')
     * );
     *
     * // Location: /users/123
     *
     * $headers = Headers::empty()->location(
     *     Uri::parse('../login')
     * );
     *
     * // Location: ../login
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::value() To serialize the URI reference.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Location header.
     *
     * @param \FireHub\Foundation\Uri $location <p>
     * URI reference identifying the target resource.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function location (Uri $location):self {

        return $this->replace(
            'Location',
            new Value($location->value())
        );

    }

    /**
     * ### Sets the Vary response header
     *
     * Specifies the request header fields that influence the selection of the response representation.
     *
     * Replaces any previously configured Vary header.
     *
     * <code>
     * $headers = Headers::empty()->vary(
     *     new Vary(
     *         new Name('Accept-Encoding'),
     *         new Name('Accept-Language')
     *     )
     * );
     *
     * // Vary: Accept-Encoding, Accept-Language
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Vary::value() To serialize the Vary header.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Vary header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Vary $vary <p>
     * Vary header definition.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function vary (Vary $vary):self {

        return $this->replace(
            'Vary',
            new Value($vary->value())
        );

    }

    /**
     * ### Sets the Allow response header
     *
     * Specifies the HTTP request methods currently supported by the target resource.
     *
     * Replaces any previously configured Allow header.
     *
     * An empty method list is permitted.
     *
     * <code>
     * $headers = Headers::empty()->allow(
     *     Standard::GET,
     *     Standard::HEAD,
     *     Standard::OPTIONS
     * );
     *
     * // Allow: GET, HEAD, OPTIONS
     *
     * $headers = Headers::empty()->allow();
     *
     * // Allow:
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Allow::value() To serialize the allowed methods.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Allow header.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method ...$methods <p>
     * HTTP request methods supported by the target resource.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function allow (Method ...$methods):self {

        return $this->replace(
            'Allow',
            new Value(
                new Allow(...$methods)->value()
            )
        );

    }

    /**
     * ### Sets the Retry-After response header
     *
     * Indicates how long a client should wait before making a follow-up request.
     *
     * Supports a non-negative delay in seconds or an absolute HTTP date.
     *
     * Replaces any previously configured Retry-After header.
     *
     * <code>
     * $headers = Headers::empty()->retryAfter(
     *     new RetryAfter(120)
     * );
     *
     * // Retry-After: 120
     *
     * $headers = Headers::empty()->retryAfter(
     *     new RetryAfter(new Date($date_time))
     * );
     *
     * // Retry-After: Wed, 04 Nov 2026 12:00:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\RetryAfter::value() To serialize the Retry-After header.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Retry-After header.
     *
     * @param \FireHub\Adapter\HTTP\Header\RetryAfter $retry_after <p>
     * Retry delay or absolute HTTP date.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function retryAfter (RetryAfter $retry_after):self {

        return $this->replace(
            'Retry-After',
            new Value($retry_after->value())
        );

    }

    /**
     * ### Sets the Accept-Ranges response header
     *
     * Advertises the range units supported by the server for the target resource.
     *
     * Replaces any previously configured Accept-Ranges header.
     *
     * <code>
     * $headers = Headers::empty()->acceptRanges(
     *     new AcceptRanges(Unit::BYTES)
     * );
     *
     * // Accept-Ranges: bytes
     *
     * $headers = Headers::empty()->acceptRanges(
     *     AcceptRanges::none()
     * );
     *
     * // Accept-Ranges: none
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\AcceptRanges::value() To serialize the Accept-Ranges header.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the Accept-Ranges header.
     *
     * @param \FireHub\Adapter\HTTP\Header\AcceptRanges $ranges <p>
     * Supported range units.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function acceptRanges (AcceptRanges $ranges):self {

        return $this->replace(
            'Accept-Ranges',
            new Value($ranges->value())
        );

    }

    /**
     * ### Sets the Strict-Transport-Security response header
     *
     * Defines an HTTP Strict Transport Security policy for the target host.
     *
     * The policy instructs supporting user agents to use HTTPS for subsequent requests during the specified maximum
     * age.
     *
     * Replaces any previously configured Strict-Transport-Security header.
     *
     * <code>
     * $headers = Headers::empty()->strictTransportSecurity(
     *     new StrictTransportSecurity(
     *         max_age: 31536000,
     *         include_subdomains: true,
     *         preload: true
     *     )
     * );
     *
     * // Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\StrictTransportSecurity::value() To serialize the HSTS policy.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::replace() To replace the HSTS header.
     *
     * @param \FireHub\Adapter\HTTP\Header\StrictTransportSecurity $policy <p>
     * HTTP Strict Transport Security policy.
     * </p>
     *
     * @return self Updated response header collection.
     */
    public function strictTransportSecurity (StrictTransportSecurity $policy):self {

        return $this->replace(
            'Strict-Transport-Security',
            new Value($policy->value())
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