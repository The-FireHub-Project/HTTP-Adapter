<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Connection;

/**
 * ### Represents standard HTTP connection options
 *
 * Defines commonly recognized HTTP/1.x connection options.
 * @since 1.0.0
 */
enum Type:string {

    /**
     * ### Close the connection after the current request
     * @since 1.0.0
     */
    case CLOSE = 'close';

    /**
     * ### Keep the connection open for future requests
     * @since 1.0.0
     */
    case KEEP_ALIVE = 'keep-alive';

    /**
     * ### Upgrade the connection to a different protocol
     * @since 1.0.0
     */
    case UPGRADE = 'upgrade';

}