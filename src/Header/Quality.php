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
use FireHub\Adapter\HTTP\Header\Exception\InvalidMediaPreferenceExceptionQuality;
use FireHub\Runtime;

/**
 * ### Represents an HTTP quality factor
 *
 * Immutable value object representing a quality factor used in HTTP content negotiation.
 *
 * Quality factors express relative preferences using values between 0 and 1, inclusive, with a maximum precision
 * of three decimal places.
 *
 * A value of 1 represents the highest preference, while 0 indicates that the corresponding representation
 * is not acceptable.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<float>
 */
final readonly class Quality extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param float $quality <p>
     * Quality factor between 0 and 1, inclusive, with at most three decimal places.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaPreferenceExceptionQuality If the quality factor is
     * invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private float $quality
    ) {

        $this->guard(
            fn() => $quality >= 0.0
                && $quality <= 1.0
                && Runtime\Math::round($quality, 3) === $quality,
            fn() => new InvalidMediaPreferenceExceptionQuality(
                'HTTP Accept quality must be between 0 and 1 with at most three decimal places.'
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():float {

        return $this->quality;

    }

}