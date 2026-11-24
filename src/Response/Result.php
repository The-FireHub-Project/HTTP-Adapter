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

/**
 * ### HTTP response result
 *
 * Represents an unresolved response value together with optional HTTP status and header metadata.
 *
 * The supplied value is converted into an HTTP response by the response resolution process.
 * Explicitly supplied metadata is applied to the resolved response.
 *
 * When metadata is omitted, the defaults provided by the corresponding value resolver are preserved.
 *
 * This class is immutable and does not perform response serialization, rendering, or emission.
 * @since 1.0.0
 *
 * @template TValue
 */
final readonly class Result {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * Value to be resolved into an HTTP response.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Status $status [optional] <p>
     * Explicit HTTP status to apply to the resolved response.
     *
     * Null preserves the status provided by the value resolver.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Response\Headers $headers [optional] <p>
     * Explicit HTTP headers to apply to the resolved response.
     *
     * Null preserves the headers provided by the value resolver.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private mixed $value,
        private ?Status $status = null,
        private ?Headers $headers = null
    ) {}

    /**
     * ### Get the unresolved response value
     *
     * <code>
     * $result = new Result('FireHub');
     *
     * $result->value(); // FireHub
     * </code>
     *
     * @since 1.0.0
     *
     * @return TValue Unresolved response value.
     */
    public function value ():mixed {

        return $this->value;

    }

    /**
     * ### Get the explicitly assigned HTTP status
     *
     * <code>
     * $result = new Result(
     *     'FireHub',
     *     Status\Standard::CREATED
     * );
     *
     * $result->status(); // Status\Standard::CREATED
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Response\Status Explicit HTTP status, or null if no status override is
     * assigned.
     */
    public function status ():?Status {

        return $this->status;

    }

    /**
     * ### Get the explicitly assigned HTTP headers
     *
     * <code>
     * $headers = Headers::empty();
     *
     * $result = new Result(
     *     value: 'FireHub',
     *     headers: $headers
     * );
     *
     * $result->headers() === $headers; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Response\Headers Explicit HTTP headers, or null if no headers are assigned.
     */
    public function headers ():?Headers {

        return $this->headers;

    }

    /**
     * ### Create a result with a different response value
     *
     * Preserves the explicitly assigned HTTP status and headers.
     *
     * <code>
     * $result = new Result('FireHub');
     *
     * $modified = $result->withValue(['name' => 'FireHub']);
     *
     * $modified->value(); // ['name' => 'FireHub']
     * </code>
     *
     * @since 1.0.0
     *
     * @template TNewValue
     *
     * @param TNewValue $value <p>
     * New unresolved response value.
     * </p>
     *
     * @return self<TNewValue> New result instance with the supplied value.
     */
    public function withValue (mixed $value):self {

        return new self(
            $value,
            $this->status,
            $this->headers
        );

    }

    /**
     * ### Create a result with an explicit HTTP status
     *
     * <code>
     * $result = new Result('FireHub');
     *
     * $modified = $result->withStatus(Status\Standard::CREATED);
     *
     * $result->status(); // null
     * $modified->status(); // Status\Standard::CREATED
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Response\Status $status <p>
     * Explicit HTTP status to assign.
     * </p>
     *
     * @return self<TValue> New result instance with the supplied HTTP status.
     */
    public function withStatus (Status $status):self {

        return new self(
            $this->value,
            $status,
            $this->headers
        );

    }

    /**
     * ### Create a result without an explicit HTTP status
     *
     * Allows the value resolver to determine the HTTP status.
     *
     * <code>
     * $result = new Result(
     *     'FireHub',
     *     Status\Standard::CREATED
     * );
     *
     * $modified = $result->withoutStatus();
     *
     * $modified->status(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return self<TValue> New result instance without an explicit HTTP status.
     */
    public function withoutStatus ():self {

        return new self(
            $this->value,
            null,
            $this->headers
        );

    }

    /**
     * ### Create a result with explicit HTTP headers
     *
     * Replaces the previously assigned result header collection.
     *
     * <code>
     * $result = new Result('FireHub');
     *
     * $headers = Headers::empty();
     *
     * $modified = $result->withHeaders($headers);
     *
     * $modified->headers() === $headers; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Response\Headers $headers <p>
     * Explicit HTTP headers to assign.
     * </p>
     *
     * @return self<TValue> New result instance with the supplied HTTP headers.
     */
    public function withHeaders (Headers $headers):self {

        return new self(
            $this->value,
            $this->status,
            $headers
        );

    }

    /**
     * ### Create a result without explicit HTTP headers
     *
     * Allows the value resolver to determine the HTTP headers without result-level overrides.
     *
     * <code>
     * $result = new Result(
     *     value: 'FireHub',
     *     headers: Headers::empty()
     * );
     *
     * $modified = $result->withoutHeaders();
     *
     * $modified->headers(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @return self<TValue> New result instance without explicit HTTP headers.
     */
    public function withoutHeaders ():self {

        return new self(
            $this->value,
            $this->status,
            null
        );

    }

}