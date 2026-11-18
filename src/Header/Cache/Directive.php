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

namespace FireHub\Adapter\HTTP\Header\Cache;

use BackedEnum;

/**
 * ### Cache-Control Directive Contract
 *
 * Defines the common contract for standard HTTP Cache-Control directive names.
 *
 * <code>
 * $directive = Request::MAX_AGE;
 *
 * $directive->value; // max-age
 * </code>
 *
 * @since 1.0.0
 */
interface Directive extends BackedEnum {}