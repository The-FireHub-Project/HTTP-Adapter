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

namespace FireHub\Adapter\HTTP\Request\Target\Exception;

use FireHub\Core\Exception\DomainException;

/**
 * ### Represents an invalid HTTP method and request-target combination
 *
 * Indicates that an HTTP request method cannot be used with the specified request-target form or protocol version.
 * @since 1.0.0
 */
final class InvalidTargetCombinationException extends DomainException {

    protected const string DEFAULT_MESSAGE = 'Invalid HTTP method and request-target combination';

}