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

namespace FireHub\Adapter\HTTP\Header\Range;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentByteRangeException;

/**
 * ### Represents an HTTP suffix byte range
 *
 * Requests the last N bytes of a resource.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class SuffixRange extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the suffix range.
     *
     * @param positive-int $length <p>
     * Number of trailing bytes.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentByteRangeException If the suffix range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $length
    ) {

        $this->guard(
            fn() => $length > 0,
            fn() => new InvalidContentByteRangeException(
                'Suffix range length must be greater than zero.'
            )
        );

    }

    /**
     * ### Gets the suffix length
     * @since 1.0.0
     *
     * @return positive-int Number of trailing bytes.
     */
    public function length ():int {

        return $this->length;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string Serialized suffix range.
     */
    public function value ():string {

        return '-'.$this->length;

    }

}