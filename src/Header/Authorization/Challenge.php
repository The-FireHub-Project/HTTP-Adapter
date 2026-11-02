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

namespace FireHub\Adapter\HTTP\Header\Authorization;

/**
 * ### Represents an HTTP authentication challenge
 *
 * Defines an authentication challenge advertised by a server through the WWW-Authenticate response header.
 * @since 1.0.0
 */
interface Challenge {

    /**
     * ### Gets the serialized authentication challenge
     * @since 1.0.0
     *
     * @return non-empty-string Serialized authentication challenge.
     */
    public function value ():string;

}