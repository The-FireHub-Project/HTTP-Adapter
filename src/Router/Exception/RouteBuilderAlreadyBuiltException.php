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

use FireHub\Core\Exception\Runtime\System\InvariantException;

/**
 * ### Route builder already built exception
 *
 * Thrown when attempting to modify or rebuild a route builder after its route definition has already been built.
 * @since 1.0.0
 */
final class RouteBuilderAlreadyBuiltException extends InvariantException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'The HTTP route builder has already been built.';

}
