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
 * ### Represents an HTTP adapter disposal failure
 *
 * Indicates that the HTTP adapter could not complete its disposal lifecycle successfully.
 *
 * This exception may occur when one or more registered disposers fail to release resources, terminate integrations,
 * or perform required cleanup operations.
 *
 * The original failure may be preserved as the previous exception, allowing callers to inspect the underlying cause.
 *
 * A disposal failure prevents the HTTP adapter from entering its disposed state.
 * @since 1.0.0
 */
final class HttpAdapterDisposalException extends RuntimeException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'The HTTP adapter failed to dispose';

}