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

namespace FireHub\Adapter\HTTP\Request\Method\Exception;

use FireHub\Core\Exception\DomainException;

/**
 * ### Represents an invalid HTTP method name
 * @since 1.0.0
 */
final class InvalidMethodNameException extends DomainException {

    protected const string DEFAULT_MESSAGE = 'Invalid HTTP method name';

}