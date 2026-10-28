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

namespace FireHub\Adapter\HTTP\Header\Exception;

use FireHub\Core\Exception\DomainException;

/**
 * ### Represents an invalid HTTP media preference quality
 * @since 1.0.0
 */
final class InvalidMediaPreferenceExceptionQuality extends DomainException {

    protected const string DEFAULT_MESSAGE = 'Invalid media preference quality.';

}