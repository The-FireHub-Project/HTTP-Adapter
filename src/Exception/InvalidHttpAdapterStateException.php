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

use FireHub\Core\Exception\Runtime\KernelException;

/**
 * ### Represents an invalid HTTP adapter lifecycle state
 *
 * Indicates that an HTTP adapter operation cannot be performed in its current lifecycle state.
 *
 * This exception may occur when attempting to initialize an already initialized adapter, dispose of an already
 * disposed adapter, or process HTTP requests before initialization has completed.
 *
 * The HTTP adapter must be in an appropriate lifecycle state before the requested operation can proceed.
 * @since 1.0.0
 */
final class InvalidHttpAdapterStateException extends KernelException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'The HTTP adapter is in an invalid state';

}