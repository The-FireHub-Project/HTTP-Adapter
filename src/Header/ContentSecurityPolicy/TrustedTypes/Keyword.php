<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\TrustedTypes;

/**
 * ### Trusted Types Keyword
 *
 * Represents special keywords supported by the Content Security Policy trusted-types directive.
 * @since 1.0.0
 */
enum Keyword:string {

    /**
     * ### Disallows the creation of Trusted Types policies
     * @since 1.0.0
     */
    case NONE = "'none'";

    /**
     * ### Allows duplicate Trusted Types policy names
     * @since 1.0.0
     */
    case ALLOW_DUPLICATES = "'allow-duplicates'";

}