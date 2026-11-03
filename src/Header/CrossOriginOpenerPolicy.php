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

namespace FireHub\Adapter\HTTP\Header;

/**
 * ### Cross-Origin Opener Policy
 *
 * Defines the Cross-Origin-Opener-Policy (COOP) HTTP response header.
 *
 * Controls whether a document can share a browsing context group with other documents, providing isolation between
 * origins.
 *
 * COOP can help protect against cross-origin attacks and is one of the requirements for enabling cross-origin
 * isolation.
 * @since 1.0.0
 */
enum CrossOriginOpenerPolicy:string {

    /**
     * ### Unsafe None
     *
     * Allows the document to share a browsing context group with compatible cross-origin documents.
     *
     * This is the default behavior when no COOP policy is specified.
     * @since 1.0.0
     */
    case UNSAFE_NONE = 'unsafe-none';

    /**
     * ### Same Origin
     *
     * Isolates the document into a browsing context group containing compatible same-origin documents.
     *
     * This policy is commonly used together with Cross-Origin-Embedder-Policy to enable cross-origin isolation.
     * @since 1.0.0
     */
    case SAME_ORIGIN = 'same-origin';

    /**
     * ### Same Origin Allow Popups
     *
     * Allows the document to retain opener relationships with certain popup windows that use unsafe-none.
     *
     * Useful for authentication and payment workflows that depend on communication with popup windows.
     * @since 1.0.0
     */
    case SAME_ORIGIN_ALLOW_POPUPS = 'same-origin-allow-popups';

    /**
     * ### Noopener Allow Popups
     *
     * Places the document into a separate browsing context group and prevents opener relationships with opened
     * documents.
     *
     * This can be useful when strict popup isolation is required.
     * @since 1.0.0
     */
    case NOOPENER_ALLOW_POPUPS = 'noopener-allow-popups';

}