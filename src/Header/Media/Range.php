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

namespace FireHub\Adapter\HTTP\Header\Media;

/**
 * ### Supported HTTP media ranges
 *
 * Represents wildcard media ranges used for HTTP content negotiation.
 *
 * This enumeration provides a type-safe representation of common media ranges supported by the FireHub HTTP Adapter
 * and prevents usage of raw wildcard strings when configuring the Accept request header.
 *
 * Unlike concrete media types, media ranges can match multiple representations and are primarily used for content
 * negotiation.
 * @since 1.0.0
 */
enum Range:string {

    /**
     * ### Any media type
     *
     * Matches any media type regardless of its top-level category.
     * @since 1.0.0
     */
    case ANY = '*/*';

    /**
     * ### Any application media type
     *
     * Matches media types belonging to the application category.
     * @since 1.0.0
     */
    case APPLICATION = 'application/*';

    /**
     * ### Any text media type
     *
     * Matches media types belonging to the text category.
     * @since 1.0.0
     */
    case TEXT = 'text/*';

    /**
     * ### Any image media type
     *
     * Matches media types belonging to the image category.
     * @since 1.0.0
     */
    case IMAGE = 'image/*';

    /**
     * ### Any audio media type
     *
     * Matches media types belonging to the audio category.
     * @since 1.0.0
     */
    case AUDIO = 'audio/*';

    /**
     * ### Any video media type
     *
     * Matches media types belonging to the video category.
     * @since 1.0.0
     */
    case VIDEO = 'video/*';

    /**
     * ### Any multipart media type
     *
     * Matches media types belonging to the multipart category.
     * @since 1.0.0
     */
    case MULTIPART = 'multipart/*';

    /**
     * ### Any font media type
     *
     * Matches media types belonging to the font category.
     * @since 1.0.0
     */
    case FONT = 'font/*';

    /**
     * ### Any message media type
     *
     * Matches media types belonging to the message category.
     * @since 1.0.0
     */
    case MESSAGE = 'message/*';

    /**
     * ### Any model media type
     *
     * Matches media types belonging to the model category.
     * @since 1.0.0
     */
    case MODEL = 'model/*';

}