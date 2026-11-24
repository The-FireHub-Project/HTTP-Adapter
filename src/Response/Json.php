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

use FireHub\Adapter\HTTP\Response;
use FireHub\Adapter\HTTP\Body\Str as StringBody;
use FireHub\Adapter\HTTP\Header\ {
    Content\Type as ContentType,
    Media\Known\MediaType
};
use FireHub\Adapter\HTTP\Response\Status\Standard;
use FireHub\Runtime;

/**
 * ### Represents a JSON HTTP response
 *
 * Serializes the supplied value into a JSON representation and encapsulates it within an HTTP response.
 *
 * The response automatically assigns the application/json content type and defaults to HTTP 200 OK.
 *
 * Additional HTTP headers are preserved, except for the Content-Type header, which is assigned by this
 * response implementation.
 *
 * <code>
 * $response = new Json([
 *     'id' => 1,
 *     'name' => 'FireHub'
 * ]);
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Json extends Response {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Headers::empty() To create an empty HTTP header collection.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::parse() To construct the JSON content type.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::withContentType() To assign the JSON content type.
     * @uses \FireHub\Runtime\Json::encode() To serialize the supplied value into JSON.
     *
     * @param mixed $value <p>
     * Value to serialize into a JSON representation.
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
    public function __construct (
        mixed $value,
        Status $status = Standard::OK,
        ?Headers $headers = null
    ) {

        $headers ??= Headers::empty();

        parent::__construct(
            $status,
            new StringBody(
                Runtime\Json::encode($value)
            ),
            $headers->withContentType(
                ContentType::parse(MediaType::APPLICATION_JSON->value)
            )
        );

    }

}