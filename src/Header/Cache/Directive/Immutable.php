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
 * ### Cache-Control Immutable Directive
 *
 * Represents the immutable response cache directive.
 *
 * Indicates that the response representation is not expected to change during its freshness lifetime.
 *
 * Caches supporting this directive can avoid unnecessary conditional revalidation while the response remains fresh.
 *
 * <code>
 * $directive = new Immutable();
 *
 * $directive->value(); // immutable
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Immutable extends Name {

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

        parent::__construct('immutable');

    }

}