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

namespace FireHub\Adapter\HTTP\Header\ContentDisposition;

/**
 * ### Standard HTTP content disposition types
 * @since 1.0.0
 */
enum Type:string {

    /**
     * ### Inline disposition
     *
     * Indicates that the content should be displayed as part of the current document whenever possible.
     * @since 1.0.0
     */
    case INLINE = 'inline';

    /**
     * ### Attachment disposition
     *
     * Indicates that the content should be handled as a download.
     * @since 1.0.0
     */
    case ATTACHMENT = 'attachment';

}