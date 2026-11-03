<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy;

/**
 * ### Represents a Content Security Policy source expression
 *
 * Defines a common contract for values used by CSP source-list  directives, including source keywords, host sources,
 * scheme sources, nonce, and cryptographic hashes.
 * @since 1.0.0
 */
interface SourceExpression {

    /**
     * ### Gets the serialized source expression
     * @since 1.0.0
     *
     * @return non-empty-string Serialized CSP source expression.
     */
    public function value ():string;

}