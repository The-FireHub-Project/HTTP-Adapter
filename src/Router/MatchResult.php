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

namespace FireHub\Adapter\HTTP\Router;

/**
 * ### Represents the result of HTTP route matching
 *
 * Defines a common contract for the possible outcomes of matching an HTTP request against registered routes.
 *
 * Implementations represent successful matches, missing routes, and requests using unsupported HTTP methods for an
 * existing path.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Router\MatchResult;
 * use FireHub\Adapter\HTTP\Router\MatchResult\Matched;
 * use FireHub\Adapter\HTTP\Router\MatchResult\NotFound;
 * use FireHub\Adapter\HTTP\Router\MatchResult\MethodNotAllowed;
 *
 * function inspect(MatchResult $result):void {
 *
 *     if ($result instanceof Matched) {
 *         // Route matched.
 *     } else if ($result instanceof MethodNotAllowed) {
 *         // Path exists, but the HTTP method is not allowed.
 *     } else if ($result instanceof NotFound) {
 *         // No matching path exists.
 *     }
 *
 * }
 * </code>
 *
 * @since 1.0.0
 */
interface MatchResult {}