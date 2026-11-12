<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Body;

use FireHub\Adapter\HTTP\Body;

/**
 * ### Represents a readable HTTP message body
 *
 * Defines sequential byte-oriented reading operations.
 *
 * Implementations may be backed by a file, network stream, or another source of binary data.
 * @since 1.0.0
 */
interface Readable extends Body {

    /**
     * ### Reads bytes from the current position
     * @since 1.0.0
     *
     * @param positive-int $length <p>
     * Maximum number of bytes to read.
     * </p>
     *
     * @return string Read bytes.
     */
    public function read (int $length):string;

    /**
     * ### Checks whether the end of the stream has been reached
     * @since 1.0.0
     *
     * @return bool True if the stream has reached its end.
     */
    public function eof ():bool;

}