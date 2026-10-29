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

namespace FireHub\Adapter\HTTP\Header\CacheControl;

/**
 * ### Represents an HTTP Cache-Control directive
 *
 * Defines the contract for standard and extension cache directives.
 * @since 1.0.0
 */
interface Directive {

    /**
     * ### Gets the serialized directive
     * @since 1.0.0
     *
     * @return non-empty-string Serialized directive.
     */
    public function value ():string;

}