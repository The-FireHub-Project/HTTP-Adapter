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

namespace FireHub\Adapter\HTTP\Body;

use FireHub\Adapter\HTTP\Body;
use FireHub\Foundation\Str as BaseStr;
use FireHub\Runtime;

/**
 * Represents an HTTP message body stored entirely in memory.
 *
 * Extends the Foundation string value object to provide immutable string operations while exposing HTTP-specific
 * body operations.
 *
 * The body size is measured in bytes rather than characters.
 *
 * This implementation is suitable for small and medium-sized HTTP request and response bodies.
 * @since 1.0.0
 *
 * @extends \FireHub\Foundation\Str<string>
 */
final readonly class Str extends BaseStr implements Body {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str::value() To return the body contents.
     */
    public function contents ():string {

        return $this->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str::value() To return the body size.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     */
    public function size ():int {

        return Runtime\Str\SB\Inspection::length($this->value());

    }

}