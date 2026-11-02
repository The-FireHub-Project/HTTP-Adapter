<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Authorization\Challenge;

use FireHub\Adapter\HTTP\Header\Authorization\Challenge;
use FireHub\Adapter\HTTP\Header\Exception\InvalidAuthenticationRealmException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Basic authentication challenge
 *
 * Advertises Basic authentication with a required realm and an optional UTF-8 charset parameter.
 * @since 1.0.0
 */
final readonly class Basic implements Challenge {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the realm.
     *
     * @param string $realm <p>
     * Authentication protection space.
     * </p>
     * @param bool $utf8 <p>
     * Whether to advertise UTF-8 credentials.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidAuthenticationRealmException If the realm contains a
     * newline or carriage return character.
     *
     * @return void
     */
    public function __construct (
        private string $realm,
        private bool $utf8 = true
    ) {

        if (Runtime\Str\SB\Regex::match(
            '/[\x00-\x1F\x7F]/',
            $realm
        )) throw new InvalidAuthenticationRealmException;

    }

    /**
     * ### Gets the serialized Basic authentication challenge
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Escape::addCSlashes() To escape the realm.
     *
     * @return non-empty-string Serialized challenge.
     */
    public function value ():string {

        $realm = Runtime\Str\SB\Escape::addCSlashes($this->realm, "\\\"");

        $value = 'Basic realm="' . $realm . '"';

        if ($this->utf8)
            $value .= ', charset="UTF-8"';

        return $value;

    }

}