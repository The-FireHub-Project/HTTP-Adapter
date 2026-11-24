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

namespace FireHub\Adapter\HTTP\Response\Resolver;

use FireHub\Adapter\HTTP\ {
    Router\ActionContext, Response
};
use FireHub\Adapter\HTTP\Response\ {
    Status\Standard, Headers, Status
};
use FireHub\Adapter\HTTP\Body\Str as StringBody;
use FireHub\Adapter\HTTP\Header\{
    Content\Type as ContentType, Media\Known\MediaType, Name
};
use FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException;
use FireHub\Runtime;
use JsonSerializable;

/**
 * ### JSON HTTP response value resolver
 *
 * Converts arrays, boolean values, and explicitly JSON-serializable objects into HTTP responses containing
 * JSON representations.
 *
 * Generated responses use the HTTP status 200 OK and the Content-Type header application/json.
 *
 * Serialization failures are propagated as exceptions rather than producing incomplete or invalid JSON
 * representations.
 *
 * Arbitrary objects are not directly supported unless they implement the JsonSerializable interface.
 * @since 1.0.0
 */
final readonly class Json implements ValueResolver {

    /**
     * {@inheritDoc}
     *
     * <code>
     * $resolver = new Json();
     *
     * $resolver->supports(['name' => 'FireHub']); // true
     * $resolver->supports([]); // true
     * $resolver->supports(true); // true
     * $resolver->supports(false); // true
     * $resolver->supports('FireHub'); // false
     * $resolver->supports(null); // false
     * $resolver->supports(new \stdClass()); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::array() To check for array values.
     * @uses \FireHub\Runtime\DataIs::bool() To check for boolean values.
     */
    public function supports (mixed $value):bool {

        return Runtime\DataIs::array($value)
            || Runtime\DataIs::bool($value)
            || $value instanceof JsonSerializable;

    }

    /**
     * {@inheritDoc}
     *
     * Converts a supported value into a JSON representation and wraps it in an in-memory HTTP response body.
     *
     * <code>
     * $resolver = new Json();
     *
     * $response = $resolver->resolve(
     *     ['name' => 'FireHub'],
     *     $context,
     *     Standard::CREATED
     * );
     *
     * $response->status(); // Standard::CREATED
     *
     * $response->headers()->getContentType()?->value();
     * // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::supports() To validate the supplied value.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::empty() To create an empty response header collection.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::parse() To construct the JSON Content-Type header.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::withContentType() To set the Content-Type header.
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException If the supplied
     * value is not supported.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the Content-Type parameters
     * are invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function resolve (mixed $value, ActionContext $context, ?Status $status = null, ?Headers $headers = null):Response {

        if (!$this->supports($value))
            throw new UnsupportedResponseValueException(
                'The supplied value cannot be resolved as JSON.'
            );

        $headers ??= Headers::empty();

        if (!$headers->has(new Name('Content-Type')))
            $headers = $headers->withContentType(
                ContentType::parse(MediaType::APPLICATION_JSON->value)
            );

        return new Response(
            $status ?? Standard::OK,
            new StringBody(
                Runtime\Json::encode($value)
            ),
            $headers
        );

    }

}