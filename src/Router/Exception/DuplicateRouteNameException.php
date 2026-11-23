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

namespace FireHub\Adapter\HTTP\Router\Exception;

use FireHub\Core\Exception\Domain\ValidationException;

/**
 * ### Duplicate HTTP route name exception
 *
 * Thrown when an HTTP route is registered with a name already assigned to another route.
 *
 * Route names must be unique within the same route storage.
 * @since 1.0.0
 */
final class DuplicateRouteNameException extends ValidationException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'HTTP route name is already registered.';

}