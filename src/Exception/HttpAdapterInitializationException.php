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

namespace FireHub\Adapter\HTTP\Exception;

use FireHub\Core\Exception\RuntimeException;

/**
 * ### Provides exceptions for HTTP adapter operations
 *
 * Defines exceptions raised during HTTP adapter initialization, request processing, lifecycle state transitions, and
 * resource disposal.
 *
 * These exceptions provide structured error reporting for invalid operations, lifecycle failures, and other HTTP
 * adapter-specific runtime conditions.
 * @since 1.0.0s
 */
final class HttpAdapterInitializationException extends RuntimeException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'The HTTP adapter failed to initialize';

}