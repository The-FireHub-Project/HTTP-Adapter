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

use FireHub\Core\Boundary\IO\Stream\ {
    Positionable, Readable
};
use FireHub\Adapter\HTTP\Body\Readable as HTTPReadable;
use FireHub\Adapter\HTTP\Body\Exception\InvalidStreamBodyException;
use FireHub\Runtime;

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
final readonly class Stream implements HTTPReadable {

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
     *
     * @throws \FireHub\Adapter\HTTP\Body\Exception\InvalidStreamBodyException If the stream is invalid.
     *
     * @return void
     */
    public function __construct (
        private Readable $stream
    ) {

        if (!$stream->isReadable() || $stream->isClosed()) {
            throw new InvalidStreamBodyException(
                'HTTP body requires an open, readable stream.'
            );
        }

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Positionable::size() To determine the underlying stream size.
     * @uses \FireHub\Core\Boundary\IO\Stream\Positionable::tell() To determine the underlying stream position.
     * @uses \FireHub\Runtime\Math::max() To ensure the returned size is non-negative.
     */
    public function size ():?int {

        if ($this->stream instanceof Positionable) {

            $size = $this->stream->size();

            if ($size !== null) {

                $position = $this->stream->tell();

                /** @var non-negative-int */
                return Runtime\Math::max(0, $size - $position);

            }

        }

        return null;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read data from the underlying stream.
     */
    public function read (int $length):string {

        return $this->stream->read($length);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check the underlying stream end-of-file.
     */
    public function eof ():bool {

        return $this->stream->eof();

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