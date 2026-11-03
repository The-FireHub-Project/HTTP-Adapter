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

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy;

/**
 * ### Defines HTTP Content Security Policy directives
 *
 * Provides standardized directives used to control resource loading,  document behavior, navigation, and security
 * violation reporting.
 * @since 1.0.0
 */
enum Directive:string {

    /**
     * ### Default source directive
     *
     * Defines the fallback source policy for fetch directives that do not have an explicitly configured policy.
     * @since 1.0.0
     */
    case DEFAULT_SRC = 'default-src';

    /**
     * ### Script source directive
     *
     * Restricts the sources from which JavaScript resources may be loaded and executed.
     * @since 1.0.0
     */
    case SCRIPT_SRC = 'script-src';

    /**
     * ### Script element source directive
     *
     * Restricts JavaScript loaded or executed through script elements.
     * @since 1.0.0
     */
    case SCRIPT_SRC_ELEM = 'script-src-elem';

    /**
     * ### Script attribute source directive
     *
     * Restricts JavaScript execution through inline event handler attributes and other script-related attributes.
     * @since 1.0.0
     */
    case SCRIPT_SRC_ATTR = 'script-src-attr';

    /**
     * ### Style source directive
     *
     * Restricts the sources from which CSS stylesheets and inline styles may be applied.
     * @since 1.0.0
     */
    case STYLE_SRC = 'style-src';

    /**
     * ### Style element source directive
     *
     * Restricts stylesheets loaded through link elements and styles defined within style elements.
     * @since 1.0.0
     */
    case STYLE_SRC_ELEM = 'style-src-elem';

    /**
     * ### Style attribute source directive
     *
     * Restricts inline CSS declarations specified through HTML style attributes.
     * @since 1.0.0
     */
    case STYLE_SRC_ATTR = 'style-src-attr';

    /**
     * ### Image source directive
     *
     * Restricts the sources from which images may be loaded, including image elements and CSS background images.
     * @since 1.0.0
     */
    case IMG_SRC = 'img-src';

    /**
     * ### Font source directive
     *
     * Restricts the sources from which web fonts may be loaded.
     * @since 1.0.0
     */
    case FONT_SRC = 'font-src';

    /**
     * ### Connection source directive
     *
     * Restricts network connections initiated by scripts, including Fetch, XMLHttpRequest, WebSocket, and EventSource.
     * @since 1.0.0
     */
    case CONNECT_SRC = 'connect-src';

    /**
     * ### Media source directive
     *
     * Restricts the sources from which audio and video resources may be loaded.
     * @since 1.0.0
     */
    case MEDIA_SRC = 'media-src';

    /**
     * ### Object source directive
     *
     * Restricts resources loaded through object and embed elements.
     *
     * Setting this directive to 'none' is recommended for applications that do not require embedded plugins.
     * @since 1.0.0
     */
    case OBJECT_SRC = 'object-src';

    /**
     * ### Frame source directive
     *
     * Restricts the sources that may be loaded into nested browsing contexts such as iframes.
     * @since 1.0.0
     */
    case FRAME_SRC = 'frame-src';

    /**
     * ### Child source directive
     *
     * Restricts the sources used to create nested browsing contexts and workers, subject to CSP fallback rules.
     * @since 1.0.0
     */
    case CHILD_SRC = 'child-src';

    /**
     * ### Worker source directive
     *
     * Restricts the sources from which dedicated workers, shared workers, and service workers may be created.
     * @since 1.0.0
     */
    case WORKER_SRC = 'worker-src';

    /**
     * ### Manifest source directive
     *
     * Restricts the sources from which web application
     * manifests may be loaded.
     * @since 1.0.0
     */
    case MANIFEST_SRC = 'manifest-src';

    /**
     * ### Base URI directive
     *
     * Restricts the URLs that may be specified by the HTML base element.
     * @since 1.0.0
     */
    case BASE_URI = 'base-uri';

    /**
     * ### Form action directive
     *
     * Restricts the destinations to which HTML forms may submit their data.
     * @since 1.0.0
     */
    case FORM_ACTION = 'form-action';

    /**
     * ### Frame ancestors directive
     *
     * Restricts which parent documents may embed the protected resource in a frame or similar context.
     *
     * Helps protect applications against clickjacking attacks.
     * @since 1.0.0
     */
    case FRAME_ANCESTORS = 'frame-ancestors';

    /**
     * ### Sandbox directive
     *
     * Applies restrictions to the protected resource, similar to the HTML iframe sandbox attribute.
     * @since 1.0.0
     */
    case SANDBOX = 'sandbox';

    /**
     * ### Upgrade insecure requests directive
     *
     * Instructs supporting user agents to upgrade eligible insecure resource requests to secure HTTPS requests.
     * @since 1.0.0
     */
    case UPGRADE_INSECURE_REQUESTS = 'upgrade-insecure-requests';

    /**
     * ### Report URI directive
     *
     * Specifies endpoints that receive reports when Content Security Policy violations occur.
     *
     * This directive is deprecated in favor of report-to, but remains relevant for browser compatibility.
     * @since 1.0.0
     */
    case REPORT_URI = 'report-uri';

    /**
     * ### Report to directive
     *
     * Specifies a reporting endpoint group used to deliver Content Security Policy violation reports.
     * @since 1.0.0
     */
    case REPORT_TO = 'report-to';

    /**
     * ### Trusted Types directive
     *
     * Restricts the names of Trusted Types policies that may be created by the protected document.
     * @since 1.0.0
     */
    case TRUSTED_TYPES = 'trusted-types';

    /**
     * ### Require Trusted Types for directive
     *
     * Requires Trusted Types values for supported injection-sensitive DOM operations.
     * @since 1.0.0
     */
    case REQUIRE_TRUSTED_TYPES_FOR = 'require-trusted-types-for';

}