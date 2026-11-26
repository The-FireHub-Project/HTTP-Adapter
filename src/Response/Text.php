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

use FireHub\Core\Type\Str\Encoding;
use FireHub\Adapter\HTTP\ {
    Body, Response
};
use FireHub\Adapter\HTTP\Header\ {
    Content\Type as ContentType,
    Media\Known\MediaType as KnownMediaType, Media\MediaType,
    Parameter\Charset,
    Attributes
};
use FireHub\Adapter\HTTP\Response\Status\Standard;

/**
 * ### Represents a plain text HTTP response
 *
 * Encapsulates plain text content within an HTTP response.
 *
 * The response automatically assigns the text/plain content type with UTF-8 character encoding and defaults
 * to HTTP 200 OK.
 *
 * The supplied text is expected to be UTF-8 encoded.
 *
 * <code>
 * $response = new Text(new Body\Str('Hello FireHub!'));
 *
 * $response->status(); // Standard::OK
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Text extends Response {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Headers::empty() To create an empty HTTP header collection.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::parse() To construct the plain text content type.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::withContentType() To assign the plain text content type.
     *
     * @param \FireHub\Adapter\HTTP\Body $body <p>
     * HTTP response body.
     * </p>
     * @param \FireHub\Adapter\HTTP\Response\Status $status [optional] <p>
     * HTTP response status.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * Additional HTTP response headers.
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
                    MediaType::parse(KnownMediaType::TEXT_PLAIN->value),
                    new Attributes(
                        new Charset(Encoding::UTF_8)
                    )
                )
            )
        );

    }

}