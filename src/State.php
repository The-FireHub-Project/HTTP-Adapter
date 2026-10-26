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

namespace FireHub\Adapter\HTTP;

/**
 * ### Defines HTTP adapter lifecycle states
 *
 * Represents the possible states of an HTTP adapter throughout its initialization, operational readiness,
 * and resource disposal lifecycle.
 *
 * HTTP adapter lifecycle states are independent of application and kernel lifecycle states and reflect the
 * execution of HTTP adapter initializers and disposers.
 * @since 1.0.0
 */
enum State {

    /**
     * ### Created
     *
     * The HTTP adapter has been instantiated but has not started initialization.
     * @since 1.0.0
     */
    case CREATED;

    /**
     * ### Initializing
     *
     * The HTTP adapter is currently executing its initializers and preparing for request processing.
     * @since 1.0.0
     */
    case INITIALIZING;

    /**
     * ### Initialized
     *
     * The HTTP adapter has successfully completed initialization and is ready to process HTTP requests.
     * @since 1.0.0
     */
    case INITIALIZED;

    /**
     * ### Disposing
     *
     * The HTTP adapter is currently executing its disposers and releasing managed resources.
     * @since 1.0.0
     */
    case DISPOSING;

    /**
     * ### Disposed
     *
     * The HTTP adapter has completed its disposal process and is no longer operational.
     * @since 1.0.0
     */
    case DISPOSED;

    /**
     * ### Initialization Failed
     *
     * The HTTP adapter failed to execute its initializers and cannot continue its normal lifecycle.
     * @since 1.0.0
     */
    case INITIALIZATION_FAILED;

    /**
     * ### Disposal Failed
     *
     * The HTTP adapter failed to execute its disposers and cannot complete its normal lifecycle.
     * @since 1.0.0
     */
    case DISPOSAL_FAILED;

}