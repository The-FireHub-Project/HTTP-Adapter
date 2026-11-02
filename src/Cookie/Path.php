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

namespace FireHub\Adapter\HTTP\Cookie;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookiePathException;
use FireHub\Runtime;

/**
* ### Represents an HTTP cookie path
* @since 1.0.0
*
* @extends \FireHub\Core\Type\ValueObject<string>
*/
final readonly class Path extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the cookie path.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the cookie path syntax.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the cookie path syntax.
     *
     * @param string $path <p>
     * Cookie path.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Cookie\Exception\InvalidCookiePathException If the path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $path
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Search::startsWith('/', $this->path),
            fn() => new InvalidCookiePathException(
                'Cookie path must start with a slash.'
            )
        );

        $this->guard(
            fn() => !Runtime\Str\SB\Regex::match(
            '/[\x00-\x1F\x7F;]/',
            $this->path
            ),
            fn() => new InvalidCookiePathException
        );

    }

    /**
     * ### Gets the cookie path
     * @since 1.0.0
     *
     * @return string Cookie path.
     */
    public function value ():string {

        return $this->path;

    }

}