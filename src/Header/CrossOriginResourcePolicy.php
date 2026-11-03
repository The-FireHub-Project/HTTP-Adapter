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
 * ### Cross-Origin Resource Policy
 *
 * Defines the Cross-Origin-Resource-Policy (CORP) HTTP response header.
 *
 * Controls whether a resource can be loaded by documents from different origins or sites using no-cors requests.
 *
 * CORP helps protect resources against certain cross-origin information disclosure attacks and supports documents
 * using Cross-Origin-Embedder-Policy: require-corp.
 *
 * This policy does not replace Cross-Origin Resource Sharing (CORS).
 * @since 1.0.0
 */
enum CrossOriginResourcePolicy:string {

    /**
     * ### Same Origin
     *
     * Restricts no-cors resource loading to documents originating from the same origin.
     *
     * The scheme, host, and effective port must match.
     *
     * Provides the strictest resource isolation.
     * @since 1.0.0
     */
    case SAME_ORIGIN = 'same-origin';

    /**
     * ### Same Site
     *
     * Allows no-cors resource loading from documents belonging to the same site.
     *
     * Same-site relationships are generally determined using the scheme and registrable domain.
     *
     * This may include different subdomains of the same site.
     * @since 1.0.0
     */
    case SAME_SITE = 'same-site';

    /**
     * ### Cross Origin
     *
     * Allows the resource to be loaded by documents from other origins using no-cors requests.
     *
     * Useful for public resources such as images, fonts, and other assets intended for cross-origin embedding.
     *
     * This does not grant JavaScript access to the response body, which is controlled separately by CORS.
     * @since 1.0.0
     */
    case CROSS_ORIGIN = 'cross-origin';

}