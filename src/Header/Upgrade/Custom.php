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

namespace FireHub\Adapter\HTTP\Header\Upgrade;

use FireHub\Adapter\HTTP\Header\Name;

/**
 * ### Represents a custom HTTP upgrade protocol name
 * @since 1.0.0
 *
 * @extends \FireHub\Adapter\HTTP\Header\Name<non-empty-string>
 */
final readonly class Custom extends Name {}