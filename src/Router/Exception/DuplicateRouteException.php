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

use FireHub\Core\Exception\Runtime\InvalidArgumentException;

/**
 * ### Thrown when an HTTP route conflicts with an existing registration
 *
 * Indicates that an HTTP method and path combination, or a route name, has already been registered.
 * @since 1.0.0
 */
final class DuplicateRouteException extends InvalidArgumentException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'HTTP route already exists.';

}