<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.3
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Integration\Native\Exception;

use FireHub\Core\Exception\RuntimeException;

/**
 * ### Thrown when an HTTP exchange operation is not permitted
 *
 * Indicates that an operation cannot be performed because the  HTTP exchange is in an invalid lifecycle state.
 * @since 1.0.0
 */
final class InvalidExchangeStateException extends RuntimeException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'The HTTP exchange is not in a valid state for this operation';

}