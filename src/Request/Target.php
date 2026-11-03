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

namespace FireHub\Adapter\HTTP\Request;

/**
 * ### Represents an HTTP request target
 *
 * Defines the contract for the four HTTP/1.1 request-target forms: origin-form, absolute-form, authority-form, and
 * asterisk-form.
 * @since 1.0.0
 */
interface Target {

    /**
     * ### Gets the serialized HTTP request target
     * @since 1.0.0
     *
     * @return non-empty-string Serialized HTTP request target.
     */
    public function value ():string;

}