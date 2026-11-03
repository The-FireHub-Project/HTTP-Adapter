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

namespace FireHub\Adapter\HTTP\Header\ClearSiteData;

/**
 * ### Clear Site Data Directive
 *
 * Defines the supported Clear-Site-Data HTTP response header directives.
 *
 * Each directive identifies a category of browser-managed data or execution state that should be cleared or refreshed.
 * @since 1.0.0
 */
enum Directive:string {

    /**
     * ### Cache
     *
     * Requests clearing locally cached data associated with the requesting site.
     * @since 1.0.0
     */
    case CACHE = 'cache';

    /**
     * ### Cookies
     *
     * Requests clearing cookies associated with the applicable domain scope, potentially affecting related subdomains.
     * @since 1.0.0
     */
    case COOKIES = 'cookies';

    /**
     * ### Storage
     *
     * Requests clearing browser-managed storage associated with the origin, including supported storage APIs.
     * @since 1.0.0
     */
    case STORAGE = 'storage';

    /**
     * ### Execution Contexts
     *
     * Requests reloading active browsing contexts associated with the origin.
     *
     * Browser support for this directive may vary.
     * @since 1.0.0
     */
    case EXECUTION_CONTEXTS = 'executionContexts';

    /**
     * ### All
     *
     * Requests clearing all data categories supported by the browser.
     *
     * This directive does not guarantee that every category is supported by every browser.
     * @since 1.0.0
     */
    case ALL = '*';

}