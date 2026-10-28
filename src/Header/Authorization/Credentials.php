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
 * ### Represents HTTP authorization credentials
 *
 * Defines the contract for serializing authentication credentials into an HTTP Authorization header value.
 *
 * Implementations are responsible for applying their authentication scheme and formatting their credentials
 * according to the corresponding protocol.
 *
 * @since 1.0.0
 */
interface Credentials {

    /**
     * ### Gets the serialized authorization credentials
     *
     * Returns the complete Authorization header value, including the authentication scheme.
     * @since 1.0.0
     *
     * @return non-empty-string Serialized authorization credentials.
     */
    public function value ():string;

}