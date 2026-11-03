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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Range\ {
    ByteRange, Unit
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentRangeException;

/**
 * ### Represents an HTTP Content-Range header
 *
 * Describes the position of a partial representation within a resource.
 *
 * Supports satisfied and unsatisfied byte ranges.
 *
 * The complete resource length may be unknown for satisfied ranges.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class ContentRange extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param null|\FireHub\Adapter\HTTP\Header\Range\ByteRange $range <p>
     * Satisfied byte range, or null for an unsatisfied range.
     * </p>
     * @param null|non-negative-int $complete_length <p>
     * Complete resource length, or null when unknown.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private ?ByteRange $range,
        private ?int $complete_length
    ) {}

    /**
     * ### Creates a satisfied Content-Range
     *
     * Represents a byte interval returned as part of a resource.
     *
     * The ending byte position must be known.
     *
     * If the complete resource length is known, the ending position must be smaller than the complete resource length.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Range\ByteRange::end() To get the ending byte position.
     *
     * @param \FireHub\Adapter\HTTP\Header\Range\ByteRange $range <p>
     * Inclusive byte interval.
     * </p>
     * @param null|non-negative-int $complete_length [optional] <p>
     * Complete resource length, or null when unknown.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentRangeException If the range is invalid.
     *
     * @return self Satisfied Content-Range.
     */
    public static function satisfied (ByteRange $range, ?int $complete_length = null):self {

        $end = $range->end();

        if ($end === null)
            throw new InvalidContentRangeException(
                'Satisfied Content-Range requires an ending byte position.'
            );

        if ($complete_length !== null) {

            if ($complete_length <= 0)
                throw new InvalidContentRangeException(
                    'Complete resource length must be greater than zero for a satisfied range.'
                );

            if ($end >= $complete_length)
                throw new InvalidContentRangeException(
                    'Byte range exceeds the complete resource length.'
                );

        }

        return new self(
            $range,
            $complete_length
        );

    }

    /**
     * ### Creates an unsatisfied Content-Range
     *
     * Indicates that the requested byte range could not be satisfied.
     *
     * Typically used with HTTP 416 Range Not Satisfiable.
     * @since 1.0.0
     *
     * @param non-negative-int $complete_length <p>
     * Complete resource length in bytes.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentRangeException If the length is negative.
     *
     * @return self Unsatisfied Content-Range.
     */
    public static function unsatisfied (int $complete_length):self {

        if ($complete_length < 0)
            throw new InvalidContentRangeException(
                'Complete resource length cannot be negative.'
            );

        return new self(
            null,
            $complete_length
        );

    }

    /**
     * ### Gets the range unit
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Range\Unit Range unit.
     */
    public function unit ():Unit {

        return Unit::BYTES;

    }

    /**
     * ### Gets the byte range
     *
     * Returns null when the range is unsatisfied.
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Range\ByteRange Byte range.
     */
    public function range ():?ByteRange {

        return $this->range;

    }

    /**
     * ### Gets the complete resource length
     *
     * Returns null when the complete length is unknown.
     * @since 1.0.0
     *
     * @return null|non-negative-int Complete resource length.
     */
    public function completeLength ():?int {

        return $this->complete_length;

    }

    /**
     * ### Checks whether the range is satisfied
     * @since 1.0.0
     *
     * @return bool True if the range is satisfied.
     */
    public function isSatisfied ():bool {

        return $this->range !== null;

    }

    /**
     * ### Checks whether the range is unsatisfied
     * @since 1.0.0
     *
     * @return bool True if the range is unsatisfied.
     */
    public function isUnsatisfied ():bool {

        return $this->range === null;

    }

    /**
     * ### Gets the length of the selected byte interval
     *
     * Returns null when the range is unsatisfied.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Range\ByteRange::end() To get the ending byte position.
     * @uses \FireHub\Adapter\HTTP\Header\Range\ByteRange::start() To get the starting byte position.
     *
     * @return null|positive-int Number of selected bytes.
     */
    public function selectedLength ():?int {

        if ($this->range === null) return null;

        $end = $this->range->end();

        if ($end === null) return null;

        /** @var null|positive-int */
        return $end - $this->range->start() + 1; // @phpstan-ignore varTag.nativeType

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Range\ByteRange::value() To get the byte range value.
     *
     * @return non-empty-string Serialized Content-Range header.
     */
    public function value ():string {

        if ($this->range === null)
            return 'bytes */'.$this->complete_length;

        return 'bytes '
            .$this->range->value()
            .'/'
            .($this->complete_length ?? '*');

    }

}