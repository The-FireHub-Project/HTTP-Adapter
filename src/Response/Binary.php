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

use FireHub\Adapter\HTTP\ {
    Body, Response
};
use FireHub\Adapter\HTTP\Header\ {
    Content\Type as ContentType,
    Media\Known\MediaType as KnownMediaType, Media\MediaType
};
use FireHub\Adapter\HTTP\Response\Status\Standard;

/**
 * ### Represents a binary HTTP response
 *
 * Encapsulates arbitrary binary content within an HTTP response.
 *
 * The response automatically assigns the application/octet-stream content type and defaults to HTTP 200 OK.
 * The content type does not include a character encoding parameter.
 *
 * Binary content may be supplied directly through an existing HTTP body or constructed from a binary string.
 * Both materialized and stream-backed message bodies are supported.
 *
 * This response implementation does not perform content encoding, media type detection, or content validation.
 * It does not automatically assign a Content-Disposition header.
 *
 * <code>
 * $response = new Binary(new Body\Str("\x00\x01\x02\xFF"));
 *
 * $response->status(); // Standard::OK
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Binary extends Response {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Headers::empty() To create an empty HTTP header collection.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::parse() To construct the binary content type.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::withContentType() To assign the binary content type.
     *
     * @param \FireHub\Adapter\HTTP\Body $body <p>
     * HTTP response body containing binary content.
     * </p>
     * @param \FireHub\Adapter\HTTP\Response\Status $status [optional] <p>
     * HTTP response status.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * Additional HTTP response headers.
     *
     * The Content-Type header is always replaced with application/octet-stream.
     * </p>
     *
     * @return void
     */
    public function __construct (Body $body, Status $status = Standard::OK, ?Headers $headers = null) {

        $headers ??= Headers::empty();

        parent::__construct(
            $status,
            $body,
            $headers->withContentType(
                new ContentType(
                    MediaType::parse(KnownMediaType::APPLICATION_OCTET_STREAM->value)
                )
            )
        );

    }

    /**
     * ### Creates a binary HTTP response from a string
     *
     * Encapsulates the supplied binary string within a materialized HTTP message body.
     *
     * The supplied bytes are preserved without character encoding conversion, serialization, or content
     * transformation.
     *
     * <code>
     * $response = Binary::fromString("\x00\x01\x02\xFF");
     *
     * $response->status(); // Standard::OK
     * $response->body()?->size(); // 4
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Body\Str To encapsulate the supplied binary content.
     *
     * @param string $value <p>
     * Binary string containing an arbitrary sequence of bytes.
     * </p>
     * @param \FireHub\Adapter\HTTP\Response\Status $status [optional] <p>
     * HTTP response status.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * Additional HTTP response headers.
     * </p>
     *
     * @return self Binary HTTP response containing the supplied bytes.
     */
    public static function fromString (string $value, Status $status = Standard::OK, ?Headers $headers = null):self {

        return new self(
            new Body\Str($value),
            $status,
            $headers
        );

    }

}