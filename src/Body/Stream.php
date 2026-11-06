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

namespace FireHub\Adapter\HTTP\Body;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Adapter\HTTP\Body;
use FireHub\Adapter\HTTP\Body\Exception\InvalidStreamBodyException;

/**
 * ### Represents a stream-backed HTTP message body
 *
 * Adapts an existing readable I/O stream for use as an HTTP request or response body.
 *
 * The body delegates all byte-oriented reading operations to the underlying stream.
 *
 * Reading may advance the underlying stream position.
 *
 * The HTTP body does not own the stream and does not close it.
 *
 * The stream lifecycle remains the responsibility of its owner.
 * @since 1.0.0
 */
final readonly class Stream implements Body {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To validate the underlying stream.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isClosed() To validate the underlying stream.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Underlying readable I/O stream.
     * </p>
     * @param null|non-negative-int $size [optional] <p>
     * Explicit total HTTP body size in bytes.
     *
     * When omitted, the underlying stream size is used.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Body\Exception\InvalidStreamBodyException If the stream is invalid.
     *
     * @return void
     */
    public function __construct (
        private Readable $stream,
        private ?int $size = null
    ) {

        if (!$stream->isReadable() || $stream->isClosed()) {
            throw new InvalidStreamBodyException(
                'HTTP body requires an open, readable stream.'
            );
        }

        if ($size !== null && $size < 0) {
            throw new InvalidStreamBodyException(
                'HTTP body size cannot be negative.'
            );
        }

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::size() To get the underlying stream size.
     */
    public function size ():?int {

        return $this->size ?? $this->stream->size();

    }

    /**
     * ### Gets the underlying readable stream
     *
     * The returned stream maintains its own reading position.
     *
     * Reading the stream may consume HTTP body content.
     * @since 1.0.0
     *
     * @return \FireHub\Core\Boundary\IO\Stream\Readable Underlying stream.
     */
    public function stream ():Readable {

        return $this->stream;

    }

}