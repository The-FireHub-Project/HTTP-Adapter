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

namespace FireHub\Adapter\HTTP\Cookie;

use FireHub\Adapter\HTTP\Cookie;

/**
 * ### Represents an HTTP request cookie
 *
 * Immutable representation of a cookie received through the Cookie request header.
 *
 * Request cookies contain only a name and value. Cookie attributes such as Domain, Path, Secure, HttpOnly, and
 * SameSite are not transmitted in the Cookie request header.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Adapter\HTTP\Cookie<TValue>
 */
final readonly class Request extends Cookie {}