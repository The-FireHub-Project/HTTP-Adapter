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

namespace FireHub\Adapter\HTTP;

/**
 * ### Defines an HTTP adapter initializer
 *
 * Establishes the contract for initializing HTTP-specific components during the adapter startup lifecycle.
 * @since 1.0.0
 */
interface Initializer {

    /**
     * ### Initializes an HTTP adapter component
     *
     * @param \FireHub\Adapter\HTTP\Adapter $adapter <p>
     * HTTP adapter being initialized.
     * </p>
     *
     * @return void
     */
    public function initialize (Adapter $adapter):void;

}