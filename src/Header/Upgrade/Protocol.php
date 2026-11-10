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

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Name;

/**
 * ### Represents an HTTP upgrade protocol
 *
 * Combines a protocol name with an optional protocol version.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Protocol extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Upgrade\Type|\FireHub\Adapter\HTTP\Header\Upgrade\Custom $type <p>
     * Protocol name.
     * </p>
     * @param null|\FireHub\Adapter\HTTP\Header\Name<non-empty-string> $version [optional] <p>
     * Optional protocol version.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Type|Custom $type,
        private ?Name $version = null
    ) {}

    /**
     * ### Gets the protocol type
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Upgrade\Type|\FireHub\Adapter\HTTP\Header\Upgrade\Custom Protocol type.
     */
    public function type ():Type|Custom {

        return $this->type;

    }

    /**
     * ### Gets the protocol version
     * @since 1.0.0
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Name<non-empty-string> Protocol version.
     */
    public function version ():?Name {

        return $this->version;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        $type = $this->type instanceof Type
            ? $this->type->value
            : $this->type->value();

        return $this->version === null
            ? $type
            : $type.'/'.$this->version->value();

    }

}