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

namespace FireHub\Adapter\HTTP\Message;

/**
 * ### Defines supported HTTP protocol versions
 *
 * Represents HTTP protocol versions used by HTTP requests and responses.
 * @since 1.0.0
 */
enum Version:string {

    /**
     * ### HTTP/1.0
     * @since 1.0.0
     */
    case HTTP_1_0 = '1.0';

    /**
     * ### HTTP/1.1
     * @since 1.0.0
     */
    case HTTP_1_1 = '1.1';

    /**
     * ### HTTP/2
     * @since 1.0.0
     */
    case HTTP_2 = '2';

    /**
     * ### HTTP/3
     * @since 1.0.0
     */
    case HTTP_3 = '3';

}