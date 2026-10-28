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

namespace FireHub\Adapter\HTTP\Header\Encoding;

/**
 * ### Defines HTTP content codings
 *
 * Represents standard HTTP content codings used in content negotiation.
 *
 * Content codings describe transformations applied to HTTP representation data, such as compression.
 * @since 1.0.0
 */
enum Type:string {


    /* * ============================================================
     * Compression
     * ============================================================ */

    /**
     * ### Gzip compression
     * @since 1.0.0
     */
    case GZIP = 'gzip';

    /**
     * ### Deflate compression
     * @since 1.0.0
     */
    case DEFLATE = 'deflate';

    /**
     * ### Brotli compression
     * @since 1.0.0
     */
    case BR = 'br';

    /**
     * ### Z-standard compression
     * @since 1.0.0
     */
    case ZSTD = 'zstd';

    /**
     * ### Compress coding
     *
     * Legacy LZW-based compression.
     * @since 1.0.0
     */
    case COMPRESS = 'compress';


    /* * ============================================================
     * Identity
     * ============================================================ */

    /**
     * ### Identity coding
     *
     * Indicates that no content coding is applied.
     * @since 1.0.0
     */
    case IDENTITY = 'identity';

}