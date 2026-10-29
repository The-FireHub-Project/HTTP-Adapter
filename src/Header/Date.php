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
use FireHub\Core\Type\Date\Zone;
use FireHub\Foundation\Temporal\ {
    DateTime, NamedTimezone
};
use FireHub\Core\Meta\Enum\Date\Format;

/**
 * ### Represents an HTTP date
 *
 * Encapsulates a date formatted according to the HTTP IMF-fixdate format.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Date extends ValueObject {

    /**
     * ### Normalized date and time
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\Temporal\DateTime<non-empty-string>
     */
    private DateTime $date;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withTimezone() To normalize the date to UTC.
     * @uses \FireHub\Foundation\Temporal\NamedTimezone::UTC() To get the UTC timezone.
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $date <p>
     * Date and time to represent as an HTTP date.
     * </p>
     *
     * @return void
     */
    public function __construct (
        DateTime $date
    ) {

        $this->date = $date->withTimezone(
            new NamedTimezone(Zone::UTC)
        );

    }

    /**
     * ### Gets the normalized date and time
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Temporal\DateTime<non-empty-string> Date and time in UTC.
     */
    public function date ():DateTime {

        return $this->date;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To format the HTTP date.
     * @uses \FireHub\Core\Meta\Enum\Date\Format::RFC7231 To use the HTTP date format.
     *
     * @return non-empty-string Serialized HTTP date.
     */
    public function value ():string {

        return $this->date->format(Format::RFC7231);

    }

}