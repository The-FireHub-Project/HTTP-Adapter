<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Range;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentByteRangeException;

/**
 * ### Represents an HTTP byte range
 *
 * Defines an inclusive byte interval within a resource.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class ByteRange extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the byte range.
     *
     * @param int<0, max> $start <p>
     * Starting byte position.
     * </p>
     * @param null|non-negative-int $end [optional] <p>
     * Inclusive ending byte position.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentByteRangeException If the byte range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $start,
        private ?int $end = null
    ) {

        $this->guard(
            fn() => $start >= 0 && ($end === null || $end >= $start),
            fn() => new InvalidContentByteRangeException
        );

    }

    /**
     * ### Gets the starting byte position
     * @since 1.0.0
     *
     * @return non-negative-int Starting position.
     */
    public function start ():int {

        return $this->start;

    }

    /**
     * ### Gets the ending byte position
     * @since 1.0.0
     *
     * @return null|non-negative-int Inclusive ending position.
     */
    public function end ():?int {

        return $this->end;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized byte range.
     */
    public function value ():string {

        return $this->start.'-'.($this->end ?? '');

    }

}