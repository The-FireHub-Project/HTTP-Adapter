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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Adapter\HTTP\Header\Range\Unit;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Accept-Ranges header
 *
 * Advertises the range units supported by the server for a target resource.
 *
 * Supports one or more range units or the special value "none".
 *
 * Duplicate range units are removed while preserving their original order.
 * @since 1.0.0
 */
final readonly class AcceptRanges {

    /**
     * ### Supported range units
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Range\Unit>
     */
    private array $units;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate range units.
     *
     * @param \FireHub\Adapter\HTTP\Header\Range\Unit ...$units <p>
     * Supported range units.
     * </p>
     *
     * @return void
     */
    public function __construct (Unit ...$units) {

        $unique = [];

        foreach ($units as $unit) {

            $name = $unit->value;

            if (!isset($unique[$name]))
                $unique[$name] = $unit;

        }

        $this->units = Runtime\Arr\Access::values($unique);

    }

    /**
     * ### Creates an Accept-Ranges header indicating no range support
     * @since 1.0.0
     *
     * @return self Accept-Ranges header with the value "none".
     */
    public static function none ():self {

        return new self;

    }

    /**
     * ### Checks whether range requests are advertised as unsupported
     * @since 1.0.0
     *
     * @return bool True if the header represents "none".
     */
    public function isNone ():bool {

        return $this->units === [];

    }

    /**
     * ### Gets the supported range units
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Range\Unit> Supported range units.
     */
    public function units ():array {

        return $this->units;

    }

    /**
     * ### Checks whether a range unit is supported
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Range\Unit $unit <p>
     * Range unit to check.
     * </p>
     *
     * @return bool True if the range unit is advertised as supported.
     */
    public function supports (Unit $unit):bool {

        foreach ($this->units as $supported)
            if ($supported === $unit)
                return true;

        return false;

    }

    /**
     * ### Serializes the Accept-Ranges header
     *
     * Returns "none" when no range units are configured.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\AcceptRanges::isNone() To check whether the header represents "none".
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the range units.
     *
     * @return non-empty-string Serialized Accept-Ranges header value.
     */
    public function value ():string {

        if ($this->isNone()) return 'none';

        $values = [];

        foreach ($this->units as $unit)
            $values[] = $unit->value;

        /** @var non-empty-string */
        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

}