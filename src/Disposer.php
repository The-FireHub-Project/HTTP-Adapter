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
 * ### Defines an HTTP adapter disposer
 *
 * Establishes the contract for releasing HTTP-specific resources during the adapter shutdown lifecycle.
 * @since 1.0.0
 */
interface Disposer {

    /**
     * ### Disposes of an HTTP adapter component
     *
     * @param \FireHub\Adapter\HTTP\Adapter $adapter <p>
     * HTTP adapter being disposed.
     * </p>
     *
     * @return void
     */
    public function dispose (Adapter $adapter):void;

}