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

namespace FireHub\Adapter\HTTP\Request\Target;

use FireHub\Adapter\HTTP\Request\Target;

/**
 * ### Represents an HTTP asterisk-form request target
 *
 * Identifies the server as a whole rather than an individual resource.
 *
 * Used with the OPTIONS request method.
 * @since 1.0.0
 */
enum Asterisk:string implements Target {

    /**
     * ### Asterisk request target
     * @since 1.0.0
     */
    case ASTERISK = '*';

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}