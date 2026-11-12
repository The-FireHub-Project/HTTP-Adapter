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
 * ### Known HTTP Media Types
 *
 * Represents registered primary Internet media types used throughout the FireHub HTTP Adapter.
 *
 * This enumeration provides a type-safe representation of standard MIME media type categories and prevents the
 * usage of raw strings when configuring HTTP request and response headers.
 *
 * Each value represents only the primary type component of a media type, without its subtype.
 *
 * Wildcard media ranges such as <code>*&#47;*</code> are intentionally excluded because they do not represent
 * concrete primary media types.
 * @since 1.0.0
 */
enum KnownType:string {

    /**
     * ### Application media type
     *
     * Represents application-specific data, documents, and binary formats.
     *
     * @since 1.0.0
     */
    case APPLICATION = 'application';

    /**
     * ### Audio media type
     *
     * Represents audio content and sound recordings.
     *
     * @since 1.0.0
     */
    case AUDIO = 'audio';

    /**
     * ### Font media type
     *
     * Represents font resources and typeface data.
     *
     * @since 1.0.0
     */
    case FONT = 'font';

    /**
     * ### Image media type
     *
     * Represents images and graphical content.
     *
     * @since 1.0.0
     */
    case IMAGE = 'image';

    /**
     * ### Message media type
     *
     * Represents encapsulated messages and message-based formats.
     *
     * @since 1.0.0
     */
    case MESSAGE = 'message';

    /**
     * ### Model media type
     *
     * Represents multidimensional models and structured spatial data.
     *
     * @since 1.0.0
     */
    case MODEL = 'model';

    /**
     * ### Multipart media type
     *
     * Represents content composed of multiple independent body parts.
     *
     * @since 1.0.0
     */
    case MULTIPART = 'multipart';

    /**
     * ### Text media type
     *
     * Represents textual content and human-readable documents.
     *
     * @since 1.0.0
     */
    case TEXT = 'text';

    /**
     * ### Video media type
     *
     * Represents video content and moving images.
     *
     * @since 1.0.0
     */
    case VIDEO = 'video';

}