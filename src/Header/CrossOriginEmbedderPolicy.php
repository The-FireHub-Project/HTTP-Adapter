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
 * ### Cross-Origin Embedder Policy
 *
 * Defines the Cross-Origin-Embedder-Policy (COEP) HTTP response header.
 *
 * Controls whether a document can load cross-origin resources that do not explicitly grant permission through CORS
 * or CORP.
 *
 * Together with Cross-Origin-Opener-Policy, COEP can enable cross-origin isolation for compatible documents.
 * @since 1.0.0
 */
enum CrossOriginEmbedderPolicy:string {

    /**
     * ### Unsafe None
     *
     * Allows resources to be loaded without additional restrictions imposed by the Cross-Origin-Embedder-Policy header.
     *
     * This is the default behavior when COEP is not specified.
     * @since 1.0.0
     */
    case UNSAFE_NONE = 'unsafe-none';

    /**
     * ### Require Cross-Origin Resource Policy
     *
     * Requires cross-origin resources to be explicitly permitted through CORS or Cross-Origin-Resource-Policy.
     *
     * Cross-origin resources loaded in no-cors mode must provide an appropriate Cross-Origin-Resource-Policy
     * response header.
     *
     * Commonly used with Cross-Origin-Opener-Policy: same-origin to enable cross-origin isolation.
     * @since 1.0.0
     */
    case REQUIRE_CORP = 'require-corp';

    /**
     * ### Credentialless
     *
     * Allows cross-origin resources to be loaded in no-cors mode without requiring an explicit CORP header, provided
     * that credentials are omitted from those requests.
     *
     * Requests using CORS mode must still satisfy CORS requirements.
     *
     * Browser support may vary.
     * @since 1.0.0
     */
    case CREDENTIALLESS = 'credentialless';

}