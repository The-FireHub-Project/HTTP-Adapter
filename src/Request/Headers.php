<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Request;

use FireHub\Adapter\HTTP\Message\Headers as MessageHeaders;

/**
 * ### Represents immutable HTTP request headers
 *
 * Provides request-specific header operations while delegating header storage and case-insensitive matching to the
 * underlying HTTP message header collection.
 * @since 1.0.0
 */
final readonly class Headers extends MessageHeaders {

}