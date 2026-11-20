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
 * ### Invalid HTTP route pattern exception
 *
 * Thrown when a route pattern violates the supported route syntax or contains invalid parameter declarations.
 * @since 1.0.0
 */
final class InvalidRoutePatternException extends ValidationException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'Invalid HTTP route pattern.';

}
