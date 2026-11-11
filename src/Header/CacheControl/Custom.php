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

namespace FireHub\Adapter\HTTP\Header\CacheControl;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException;
use FireHub\Runtime;

/**
 * ### Represents a custom HTTP Cache-Control directive
 *
 * Supports extension cache directives that are not defined by the standard request and response directive enums.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Custom extends ValueObject  implements Directive {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the custom directive name.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the directive name to lowercase.
     *
     * @param non-empty-string $name <p>
     * Custom directive name.
     * </p>
     * @param null|string $value <p>
     * Optional directive value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the name is invalid or the
     * value is invalid.
     *
     * @return void
     */
    public function __construct (
        private string $name,
        private ?string $value = null
    ) {

        if (!Runtime\Str\SB\Regex::match(
            '/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/',
            $name
        )) throw new InvalidCacheDirectiveException(
            'Invalid Cache-Control directive name.'
        );

        $normalized = Runtime\Str\SB\Casing::toLower($name);

        if (
            Request::tryFrom($normalized) !== null
            || Response::tryFrom($normalized) !== null
        ) throw new InvalidCacheDirectiveException(
            "Cache-Control directive '$name' is reserved for standard directives."
        );

        if (
            $value !== null
            && !Runtime\Str\SB\Regex::match(
                '/\A[\x20-\x7E]*\z/',
                $value
            )
        ) throw new InvalidCacheDirectiveException(
            'Invalid Cache-Control directive value.'
        );

    }

    /**
     * ### Gets the custom directive name
     * @since 1.0.0
     *
     * @return non-empty-string Custom directive name.
     */
    public function name ():string {

        return $this->name;

    }

    /**
     * ### Gets the optional directive parameter
     *
     * Returns null when the directive has no parameter.
     * @since 1.0.0
     *
     * @return null|string Directive parameter or null.
     */
    public function parameter ():?string {

        return $this->value;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To determine whether the parameter is an HTTP token.
     * @uses \FireHub\Runtime\Str\SB\Escape::addCSlashes() To escape a quoted parameter.
     */
    public function value ():string {

        if ($this->value === null) return $this->name;

        if (Runtime\Str\SB\Regex::match(
            '/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/',
            $this->value
        )) return $this->name.'='.$this->value;

        return $this->name.'="'.
            Runtime\Str\SB\Escape::addCSlashes(
                $this->value,
                "\\\""
            ).'"';

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Data::serialize() To serialize the comparison components.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the directive name.
     */
    protected function comparisonValue ():string {

        return Runtime\Data::serialize([
            Runtime\Str\SB\Casing::toLower($this->name),
            $this->value
        ]);

    }

}