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
use FireHub\Adapter\HTTP\Body\Str as StringBody;
use FireHub\Adapter\HTTP\Response\ {
    Status\Standard, Headers
};
use FireHub\Adapter\HTTP\Header\Content\Type as ContentType;
use FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException;
use FireHub\Runtime;

/**
 * ### Plain text HTTP response value resolver
 *
 * Converts string, integer, and floating-point route handler return values into HTTP responses containing
 * plain text.
 *
 * Supported values are converted to their string representations and wrapped in an in-memory HTTP body.
 *
 * Generated responses use the HTTP status 200 OK and the Content-Type header text/plain; charset=utf-8.
 *
 * Boolean values, arrays, objects, and null are not supported.
 *
 * <code>
 * $resolver = new Text();
 *
 * $resolver->supports('Hello FireHub!'); // true
 * $resolver->supports(123); // true
 * $resolver->supports(12.5); // true
 * $resolver->supports(true); // false
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Text implements ValueResolver {

    /**
     * {@inheritDoc}
     *
     * <code>
     * $resolver = new Text();
     *
     * $resolver->supports('Hello FireHub!'); // true
     * $resolver->supports(123); // true
     * $resolver->supports(12.5); // true
     * $resolver->supports(null); // false
     * $resolver->supports([]); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::string() To check for string values.
     * @uses \FireHub\Runtime\DataIs::int() To check for integer values.
     * @uses \FireHub\Runtime\DataIs::float() To check for floating-point values.
     */
    public function supports (mixed $value):bool {

        return Runtime\DataIs::string($value)
            || Runtime\DataIs::int($value)
            || Runtime\DataIs::float($value);

    }

    /**
     * {@inheritDoc}
     *
     * Converts a supported scalar value into a plain text HTTP response.
     *
     * <code>
     * $resolver = new Text();
     *
     * $response = $resolver->resolve(
     *     'Hello FireHub!',
     *     $context
     * );
     *
     * $response->status()->code(); // 200
     *
     * $response->headers()
     *     ->getContentType()
     *     ?->value(); // text/plain; charset=utf-8
     *
     * $response->body()?->contents(); // Hello FireHub!
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::supports() To validate the supplied value.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::parse() To construct the plain text Content-Type header.
     * @uses \FireHub\Adapter\HTTP\Response\Headers::empty() To create the response header collection.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::withContentType() To set the Content-Type header.
     * @uses \FireHub\Adapter\HTTP\Response::__construct() To construct the HTTP response.
     *
     * @throws \FireHub\Adapter\HTTP\Response\Resolver\Exception\UnsupportedResponseValueException If the supplied
     * value is not supported.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function resolve (mixed $value, ActionContext $context):Response {

        if (!$this->supports($value))
            throw new UnsupportedResponseValueException(
                'The supplied value cannot be resolved as plain text.'
            );

        /** @var string|int|float $value */
        return new Response(
            Standard::OK,
            new StringBody((string)$value),
            Headers::empty()->withContentType(
                ContentType::parse('text/plain; charset=utf-8')
            )
        );

    }

}