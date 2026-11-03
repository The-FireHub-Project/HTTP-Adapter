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
 * ### Content Security Policy Sandbox Token
 *
 * Defines permissions that can be restored to a document restricted by the Content Security Policy sandbox directive.
 *
 * An empty sandbox directive applies all sandbox restrictions. Individual tokens selectively remove specific
 * restrictions.
 *
 * Sandbox tokens are not CSP source expressions and must not be used with source-list directives.
 * @since 1.0.0
 */
enum SandboxToken:string {

    /**
     * ### Allow Downloads
     *
     * Allows the sandboxed document to initiate downloads, including downloads triggered by user interaction.
     * @since 1.0.0
     */
    case ALLOW_DOWNLOADS = 'allow-downloads';

    /**
     * ### Allow Forms
     *
     * Allows the sandboxed document to submit forms.
     *
     * Without this token, form submission is restricted.
     * @since 1.0.0
     */
    case ALLOW_FORMS = 'allow-forms';

    /**
     * ### Allow Modals
     *
     * Allows the sandboxed document to display modal dialogs, including JavaScript alert, confirm, and prompt dialogs.
     * @since 1.0.0
     */
    case ALLOW_MODALS = 'allow-modals';

    /**
     * ### Allow Orientation Lock
     *
     * Allows the sandboxed document to lock the screen orientation through supported browser APIs.
     * @since 1.0.0
     */
    case ALLOW_ORIENTATION_LOCK = 'allow-orientation-lock';

    /**
     * ### Allow Pointer Lock
     *
     * Allows the sandboxed document to use the Pointer Lock API.
     *
     * This permits applications such as games to capture pointer movement.
     * @since 1.0.0
     */
    case ALLOW_POINTER_LOCK = 'allow-pointer-lock';

    /**
     * ### Allow Popups
     *
     * Allows the sandboxed document to open auxiliary browsing contexts, including popup windows.
     *
     * Newly opened contexts inherit applicable sandbox restrictions unless explicitly permitted otherwise.
     * @since 1.0.0
     */
    case ALLOW_POPUPS = 'allow-popups';

    /**
     * ### Allow Popups to Escape Sandbox
     *
     * Allows newly opened browsing contexts to escape sandbox restrictions inherited from the document.
     *
     * This token does not independently grant permission to open popups.
     * @since 1.0.0
     */
    case ALLOW_POPUPS_TO_ESCAPE_SANDBOX = 'allow-popups-to-escape-sandbox';

    /**
     * ### Allow Presentation
     *
     * Allows the sandboxed document to initiate presentation sessions through supported browser APIs.
     * @since 1.0.0
     */
    case ALLOW_PRESENTATION = 'allow-presentation';

    /**
     * ### Allow Same Origin
     *
     * Prevents the sandbox from forcing the document into a unique opaque origin.
     *
     * Combining this token with ALLOW_SCRIPTS can weaken sandbox isolation, especially for same-origin content.
     * @since 1.0.0
     */
    case ALLOW_SAME_ORIGIN = 'allow-same-origin';

    /**
     * ### Allow Scripts
     *
     * Allows the sandboxed document to execute scripts, subject to other applicable security restrictions.
     *
     * This token does not override restrictions imposed by script-src or other CSP directives.
     * @since 1.0.0
     */
    case ALLOW_SCRIPTS = 'allow-scripts';

    /**
     * ### Allow Storage Access by User Activation
     *
     * Allows the sandboxed document to request access to unpartitioned storage through the Storage Access API when
     * the required user activation conditions are met.
     * @since 1.0.0
     */
    case ALLOW_STORAGE_ACCESS_BY_USER_ACTIVATION = 'allow-storage-access-by-user-activation';

    /**
     * ### Allow Top Navigation
     *
     * Allows the sandboxed document to navigate its top-level browsing context.
     *
     * This permission is not limited to navigation initiated by user activation.
     * @since 1.0.0
     */
    case ALLOW_TOP_NAVIGATION = 'allow-top-navigation';

    /**
     * ### Allow Top Navigation by User Activation
     *
     * Allows the sandboxed document to navigate its top-level browsing context when the navigation is initiated
     * through user activation.
     * @since 1.0.0
     */
    case ALLOW_TOP_NAVIGATION_BY_USER_ACTIVATION = 'allow-top-navigation-by-user-activation';

    /**
     * ### Allow Top Navigation to Custom Protocols
     *
     * Allows navigation to supported external or custom protocols under the applicable browser restrictions.
     * @since 1.0.0
     */
    case ALLOW_TOP_NAVIGATION_TO_CUSTOM_PROTOCOLS = 'allow-top-navigation-to-custom-protocols';

}