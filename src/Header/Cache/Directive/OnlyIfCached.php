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

namespace FireHub\Adapter\HTTP\Header\Cache\Directive;

use FireHub\Adapter\HTTP\Header\Name;

/**
 * ### Cache-Control, Only-If-Cached Directive
 *
 * Represents the only-if-cached request cache directive.
 *
 * Instructs a cache to respond using a stored response without contacting the origin server.
 *
 * If no suitable stored response is available, the cache should generate a 504 Gateway Timeout response.
 *
 * <code>
 * $directive = new OnlyIfCached();
 *
 * $directive->value(); // only-if-cached
 * </code>
 *
 * @since 1.0.0
 */
final readonly class OnlyIfCached extends Name {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct () {

        parent::__construct('only-if-cached');

    }

}